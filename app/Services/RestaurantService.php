<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RestaurantService
{
    public function getPaginatedRestaurants(int $perPage = 12): LengthAwarePaginator
    {
        return Restaurant::withCount('tables')
            ->with('tables:restaurant_id,max_people_count')
            ->paginate($perPage);
    }


    public function getAllRestaurants(): Collection
    {
        return Restaurant::withCount('tables')
            ->with('tables')
            ->get();
    }


    public function getRestaurantWithRelations(int $id, array $relations = ['tables']): ?Restaurant
    {
        return Restaurant::with($relations)->find($id);
    }


    public function createRestaurant(array $data): array
    {
        $validator = $this->validateRestaurantData($data);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
        }

        try {
            DB::beginTransaction();

            $restaurant = Restaurant::create([
                'name' => $data['name']
            ]);

            $tablesCreated = 0;
            if (!empty($data['tables']) && is_array($data['tables'])) {
                foreach ($data['tables'] as $tableData) {
                    $restaurant->tables()->create([
                        'name' => $tableData['name'],
                        'min_people_count' => $tableData['min_people_count'],
                        'max_people_count' => $tableData['max_people_count'],
                    ]);
                    $tablesCreated++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Restaurant created successfully with {$tablesCreated} tables.",
                'restaurant' => $restaurant->load('tables')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to create restaurant. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }


    public function updateRestaurant(Restaurant $restaurant, array $data): array
    {
        $validator = $this->validateRestaurantData($data, $restaurant->id);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
        }

        try {
            DB::beginTransaction();

            $restaurant->update([
                'name' => $data['name']
            ]);

            $this->updateRestaurantTables($restaurant, $data['tables'] ?? []);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Restaurant updated successfully.',
                'restaurant' => $restaurant->fresh(['tables'])
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to update restaurant. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }


    public function deleteRestaurant(Restaurant $restaurant): array
    {
        try {
            $restaurantName = $restaurant->name;
            $restaurant->delete();

            return [
                'success' => true,
                'message' => "Restaurant '{$restaurantName}' deleted successfully."
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete restaurant. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }


    private function validateRestaurantData(array $data, ?int $restaurantId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:restaurants,name'
            ],
            'tables' => 'array',
            'tables.*.id' => 'nullable|exists:tables,id',
            'tables.*.name' => 'required|string|max:255',
            'tables.*.min_people_count' => 'required|integer|min:1',
            'tables.*.max_people_count' => 'required|integer|min:1|gte:tables.*.min_people_count',
        ];

        $messages = [
            'name.required' => 'The restaurant name is required.',
            'name.unique' => 'This restaurant name is already taken.',
            'tables.*.name.required' => 'Each table must have a name.',
            'tables.*.min_people_count.required' => 'Minimum people count is required.',
            'tables.*.min_people_count.min' => 'Minimum people count must be at least 1.',
            'tables.*.max_people_count.required' => 'Maximum people count is required.',
            'tables.*.max_people_count.min' => 'Maximum people count must be at least 1.',
            'tables.*.max_people_count.gte' => 'Maximum people count must be greater than or equal to minimum people count.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        $validator->after(function ($validator) use ($data, $restaurantId) {
            if (!empty($data['tables'])) {
                $this->validateTableNames($validator, $data['tables'], $restaurantId);
            }
        });

        return $validator;
    }


    private function validateTableNames($validator, array $tables, ?int $restaurantId = null): void
    {
        $tableNames = array_column($tables, 'name');

        if (count($tableNames) !== count(array_unique($tableNames))) {
            $validator->errors()->add('tables', 'Table names must be unique within the restaurant.');
        }

        if ($restaurantId) {
            foreach ($tables as $index => $table) {
                $query = Table::where('restaurant_id', $restaurantId)
                    ->where('name', $table['name']);

                if (!empty($table['id'])) {
                    $query->where('id', '!=', $table['id']);
                }

                if ($query->exists()) {
                    $validator->errors()->add("tables.{$index}.name", 'This table name already exists in the restaurant.');
                }
            }
        }
    }


    private function updateRestaurantTables(Restaurant $restaurant, array $tables): void
    {
        $currentTables = $restaurant->tables()->pluck('id', 'id')->toArray();
        $submittedTableIds = [];

        foreach ($tables as $tableData) {
            if (isset($tableData['id']) && in_array($tableData['id'], $currentTables)) {
                $table = Table::find($tableData['id']);
                $table->update([
                    'name' => $tableData['name'],
                    'min_people_count' => $tableData['min_people_count'],
                    'max_people_count' => $tableData['max_people_count'],
                ]);
                $submittedTableIds[] = $tableData['id'];
            } else {
                $newTable = $restaurant->tables()->create([
                    'name' => $tableData['name'],
                    'min_people_count' => $tableData['min_people_count'],
                    'max_people_count' => $tableData['max_people_count'],
                ]);
                $submittedTableIds[] = $newTable->id;
            }
        }

        $tablesToDelete = array_diff($currentTables, $submittedTableIds);
        if (!empty($tablesToDelete)) {
            Table::whereIn('id', $tablesToDelete)->delete();
        }
    }
}
