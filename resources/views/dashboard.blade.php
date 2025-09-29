<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Welcome back!</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __("You're logged in to the restaurant management system.") }}</p>
                    </div>

                    @if(auth()->user()->is_admin)
                        <!-- Restaurant Management Section -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Restaurant Management</h4>
                                    <p class="text-blue-600 dark:text-blue-300 text-sm">Manage your restaurants and table configurations</p>
                                </div>
                                <a href="{{ route('restaurants.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Restaurant
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                <!-- Quick Stats -->
                                @php
                                    $restaurantCount = App\Models\Restaurant::count();
                                    $tableCount = App\Models\Table::count();
                                    $totalCapacity = App\Models\Table::sum('max_people_count');
                                    $recentReservations = App\Models\Reservation::where('reservation_date', '>=', now())->count();
                                @endphp
                                
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $restaurantCount }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Restaurants</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $tableCount }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Tables</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalCapacity }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Capacity</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
                                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $recentReservations }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Upcoming</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('restaurants.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Manage Restaurants
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Welcome!</h4>
                            <p class="text-gray-600 dark:text-gray-400">You don't have admin privileges to manage restaurants. Contact your administrator for access.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
