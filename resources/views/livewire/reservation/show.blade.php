
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <!-- Header -->
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Reservation Details
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Reservation #{{ $reservation->id }} at {{ $reservation->restaurant->name }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('reservations.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium text-sm rounded-lg transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Reservations
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Reservation Details -->
                    <div class="lg:col-span-2">
                        <div class="space-y-6">
                            <!-- Customer Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Customer Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-600 px-3 py-2 rounded border">
                                            {{ $reservation->reservation_name }} {{ $reservation->reservation_surname }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-600 px-3 py-2 rounded border">
                                            {{ $reservation->email ?: 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-600 px-3 py-2 rounded border">
                                            {{ $reservation->phone ?: 'Not provided' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Party Size</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-600 px-3 py-2 rounded border">
                                            {{ $reservation->total_people_count }} {{ Str::plural('person', $reservation->total_people_count) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Reservation Details -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Reservation Details
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Restaurant</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-600 px-3 py-2 rounded border">
                                            {{ $reservation->restaurant->name }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-600 px-3 py-2 rounded border">
                                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-600 px-3 py-2 rounded border">
                                            {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-600 px-3 py-2 rounded border">
                                            {{ $reservation->duration_hours }} {{ Str::plural('hour', $reservation->duration_hours) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Table Assignment -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Assigned Tables
                                </h3>
                                @if($reservation->tables->count() > 0)
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @foreach($reservation->tables as $table)
                                            <div class="bg-white dark:bg-gray-600 p-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                                <div class="text-center">
                                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $table->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        Seats: {{ $table->min_people_count }}-{{ $table->max_people_count }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 italic">No tables assigned</p>
                                @endif
                            </div>

                            <!-- Guest List -->
                            @if($reservation->guests->count() > 0)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Guests ({{ $reservation->guests->count() }})
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($reservation->guests as $guest)
                                            <div class="bg-white dark:bg-gray-600 p-4 rounded-lg border border-gray-200 dark:border-gray-500">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $guest->name }} {{ $guest->surname }}
                                                        </h4>
                                                        @if($guest->email)
                                                            <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 3.26a2 2 0 001.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                                </svg>
                                                                <a href="mailto:{{ $guest->email }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                                    {{ $guest->email }}
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="mt-2 text-sm text-gray-400 dark:text-gray-500 italic">
                                                                No email provided
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-2">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                            Guest
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Stats -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Stats</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total People:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $reservation->total_people_count }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Guests:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $reservation->guests->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Tables:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $reservation->tables->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Created:</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $reservation->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Restaurant Info -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Restaurant</h3>
                            <div class="space-y-3">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $reservation->restaurant->name }}</h4>
                                    @if($reservation->restaurant->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $reservation->restaurant->description }}</p>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Total Tables:</strong> {{ $reservation->restaurant->tables->count() }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Total Capacity:</strong> {{ $reservation->restaurant->tables->sum('max_people_count') }} people
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
