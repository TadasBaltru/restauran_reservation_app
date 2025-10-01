<?php

namespace Tests\Unit;

use App\Services\RestaurantService;
use Tests\TestCase;

class RestaurantValidationTest extends TestCase
{
    protected RestaurantService $restaurantService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurantService = new RestaurantService();
    }

    public function test_validates_duplicate_table_names_within_restaurant()
    {
        $data = [
            'name' => 'Test Restaurant',
            'tables' => [
                [
                    'name' => 'Table A',
                    'min_people_count' => 2,
                    'max_people_count' => 4,
                ],
                [
                    'name' => 'Table A', // Duplicate name
                    'min_people_count' => 4,
                    'max_people_count' => 6,
                ]
            ]
        ];

        $result = $this->restaurantService->createRestaurant($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Validation failed', $result['message']);
        $this->assertTrue($result['errors']->has('tables'));
        $this->assertStringContainsString('unique within the restaurant', $result['errors']->first('tables'));
    }

    public function test_validates_multiple_validation_errors()
    {
        $data = [
            'name' => '', // Invalid: empty name
            'tables' => [
                [
                    'name' => '', // Invalid: empty table name
                    'min_people_count' => 6, // Invalid when max is 4
                    'max_people_count' => 4,
                ],
                [
                    'name' => 'Valid Table Name',
                    'min_people_count' => 0, // Invalid: zero min
                    'max_people_count' => 6,
                ]
            ]
        ];

        $result = $this->restaurantService->createRestaurant($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Validation failed', $result['message']);
        
        // Check for multiple validation errors
        $this->assertTrue($result['errors']->has('name'));
        $this->assertTrue($result['errors']->has('tables.0.name'));
        $this->assertTrue($result['errors']->has('tables.0.max_people_count'));
        $this->assertTrue($result['errors']->has('tables.1.min_people_count'));
    }
}

