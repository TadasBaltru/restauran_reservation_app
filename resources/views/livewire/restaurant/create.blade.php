<div class="py-12" x-data="{
    tables: @entangle('tables'),
    addTable() {
        $wire.addTable();
    },
    removeTable(index) {
        $wire.removeTable(index);
    }
}">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <form wire:submit="save" class="p-6 lg:p-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Add New Restaurant</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Create a new restaurant with tables</p>
                    </div>
                    <button type="button" 
                            wire:click="cancel"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Restaurant Name -->
                <div class="mb-8">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Restaurant Name
                    </label>
                    <input type="text" 
                           id="name" 
                           wire:model="name"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out @error('name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                           placeholder="Enter restaurant name">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tables Section -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Tables</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Configure the tables available in this restaurant</p>
                        </div>
                        <button type="button" 
                                @click="addTable()"
                                class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Table
                        </button>
                    </div>

                    @error('tables')
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                </svg>
                                {{ $message }}
                            </div>
                        </div>
                    @enderror

                    <div class="space-y-4">
                        @foreach($tables as $index => $table)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Table {{ $index + 1 }}
                                    </h3>
                                    @if(count($tables) > 1)
                                        <button type="button" 
                                                @click="removeTable({{ $index }})"
                                                class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Table Name -->
                                    <div>
                                        <label for="table_name_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Table Name
                                        </label>
                                        <input type="text" 
                                               id="table_name_{{ $index }}" 
                                               wire:model="tables.{{ $index }}.name"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('tables.'.$index.'.name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                               placeholder="e.g., Table A">
                                        @error('tables.'.$index.'.name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Min People -->
                                    <div>
                                        <label for="min_people_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Min People
                                        </label>
                                        <input type="number" 
                                               id="min_people_{{ $index }}" 
                                               wire:model="tables.{{ $index }}.min_people_count"
                                               min="1"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('tables.'.$index.'.min_people_count') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                        @error('tables.'.$index.'.min_people_count')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Max People -->
                                    <div>
                                        <label for="max_people_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Max People
                                        </label>
                                        <input type="number" 
                                               id="max_people_{{ $index }}" 
                                               wire:model="tables.{{ $index }}.max_people_count"
                                               min="1"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('tables.'.$index.'.max_people_count') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                        @error('tables.'.$index.'.max_people_count')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" 
                            wire:click="cancel"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <span wire:loading.remove>
                            Create Restaurant
                        </span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
