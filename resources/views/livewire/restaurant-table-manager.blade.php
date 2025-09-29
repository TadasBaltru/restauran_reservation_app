<div>
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tables</h3>
            <button 
                type="button"
                wire:click="addTable"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Table
            </button>
        </div>
    </div>

    <div class="space-y-4" x-data="{ expandedTables: {} }">
        @foreach($tables as $index => $table)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <!-- Table Header -->
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                            Table {{ $index + 1 }}
                        </h4>
                        @if(count($tables) > 1)
                            <button 
                                type="button"
                                wire:click="removeTable({{ $index }})"
                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-md transition-colors duration-150"
                            >
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Remove
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Table Form Fields -->
                <div class="p-4 space-y-4">
                    <!-- Hidden ID field for existing tables -->
                    @if($table['id'])
                        <input type="hidden" name="tables[{{ $index }}][id]" wire:model="tables.{{ $index }}.id">
                    @endif

                    <!-- Table Name -->
                    <div>
                        <label for="table_name_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Table Name/Number
                        </label>
                        <input 
                            type="text" 
                            id="table_name_{{ $index }}"
                            name="tables[{{ $index }}][name]"
                            wire:model.blur="tables.{{ $index }}.name"
                            placeholder="e.g., Table 1, A1, Patio-3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            required
                        >
                        @error('tables.' . $index . '.name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacity Settings -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Min People Count -->
                        <div>
                            <label for="min_people_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Min People
                            </label>
                            <input 
                                type="number" 
                                id="min_people_{{ $index }}"
                                name="tables[{{ $index }}][min_people_count]"
                                wire:model.blur="tables.{{ $index }}.min_people_count"
                                min="1"
                                max="50"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                required
                            >
                            @error('tables.' . $index . '.min_people_count')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max People Count -->
                        <div>
                            <label for="max_people_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Max People
                            </label>
                            <input 
                                type="number" 
                                id="max_people_{{ $index }}"
                                name="tables[{{ $index }}][max_people_count]"
                                wire:model.blur="tables.{{ $index }}.max_people_count"
                                min="1"
                                max="50"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                required
                            >
                            @error('tables.' . $index . '.max_people_count')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Capacity Preview -->
                    @if($table['min_people_count'] && $table['max_people_count'])
                        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>
                                Seats {{ $table['min_people_count'] }}
                                @if($table['min_people_count'] != $table['max_people_count'])
                                    to {{ $table['max_people_count'] }}
                                @endif
                                people
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if(count($tables) === 0)
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tables</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a table.</p>
            <div class="mt-6">
                <button 
                    type="button"
                    wire:click="addTable"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Table
                </button>
            </div>
        </div>
    @endif
</div>
