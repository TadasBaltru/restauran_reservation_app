<!-- Form Card -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif
        
        <!-- Form Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $restaurant ? 'Edit ' . $restaurant->name : 'Restaurant Information' }}
            </h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $restaurant ? 'Update restaurant information and configure its tables.' : 'Create a new restaurant and configure its tables.' }}
            </p>
        </div>

        <div class="px-6 space-y-6">
            <!-- Global Errors -->
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded-md">
                    <h4 class="font-medium mb-2">Please correct the following errors:</h4>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Restaurant Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Restaurant Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $restaurant->name ?? '') }}"
                    placeholder="Enter restaurant name"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                    required
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Livewire Table Manager with Initial Tables -->
            <div>
                @if($restaurant)
                    @php
                        $initialTables = $restaurant->tables->map(function($table) {
                            return [
                                'id' => $table->id,
                                'name' => $table->name,
                                'min_people_count' => $table->min_people_count,
                                'max_people_count' => $table->max_people_count,
                            ];
                        })->toArray();
                    @endphp
                    @livewire('restaurant-table-manager', ['initialTables' => $initialTables])
                @else
                    @livewire('restaurant-table-manager')
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
            <a href="{{ $cancelRoute }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Cancel
            </a>
            
            <button 
                type="submit" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ $submitText }}
            </button>
        </div>
    </form>
</div>