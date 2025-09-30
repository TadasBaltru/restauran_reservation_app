<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12" x-data="{
    guests: @entangle('guests'),
    selectedRestaurant: @entangle('selectedRestaurant'),
    addGuest() {
        $wire.addGuest();
    },
    removeGuest(index) {
        $wire.removeGuest(index);
    },
    checkAvailability() {
        $wire.checkAvailability();
    }
}">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                Make a Reservation
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Choose your favorite restaurant and book a table for an unforgettable dining experience
            </p>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if (session()->has('availability'))
            <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('availability') }}
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <form wire:submit="save" class="p-6 lg:p-8">
                
                <!-- Restaurant Selection -->
                <div class="mb-8">
                    <label for="restaurant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Restaurant *
                    </label>
                    <select wire:model.live="restaurant_id"
                            id="restaurant_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('restaurant_id') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <option value="">Choose a restaurant...</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">
                                {{ $restaurant->name }} ({{ $restaurant->tables_count }} tables - up to {{ $restaurant->max_people_capacity }} people)
                            </option>
                        @endforeach
                    </select>
                    @error('restaurant_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <!-- Restaurant Info -->
                    <div x-show="selectedRestaurant" x-transition class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <template x-if="selectedRestaurant">
                            <div>
                                <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2" x-text="selectedRestaurant.name"></h3>
                                <div class="text-sm text-blue-700 dark:text-blue-300">
                                    <p><span class="font-medium">Tables available:</span> <span x-text="selectedRestaurant.tables_count"></span></p>
                                    <p><span class="font-medium">Maximum capacity:</span> <span x-text="selectedRestaurant.max_people_capacity"></span> people</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="reservation_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                First Name *
                            </label>
                            <input type="text" 
                                   id="reservation_name" 
                                   wire:model="reservation_name"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('reservation_name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                   placeholder="Enter your first name">
                            @error('reservation_name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="reservation_surname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Last Name *
                            </label>
                            <input type="text" 
                                   id="reservation_surname" 
                                   wire:model="reservation_surname"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('reservation_surname') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                   placeholder="Enter your last name">
                            @error('reservation_surname')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address
                            </label>
                            <input type="email" 
                                   id="email" 
                                   wire:model="email"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('email') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                   placeholder="your@email.com">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   wire:model="phone"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('phone') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                   placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Reservation Details -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Reservation Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Date -->
                        <div>
                            <label for="reservation_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Date *
                            </label>
                            <input type="date" 
                                   id="reservation_date" 
                                   wire:model.live="reservation_date"
                                   min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('reservation_date') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                            @error('reservation_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div>
                            <label for="reservation_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Time *
                            </label>
                            <select wire:model.live="reservation_time"
                                    id="reservation_time"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('reservation_time') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="">Select time...</option>
                                @foreach($timeSlots as $value => $display)
                                    <option value="{{ $value }}">{{ $display }}</option>
                                @endforeach
                            </select>
                            @error('reservation_time')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Duration *
                            </label>
                            <select wire:model.live="duration_hours"
                                    id="duration_hours"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('duration_hours') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                <option value="1">1 hour</option>
                                <option value="2">2 hours</option>
                                <option value="3">3 hours</option>
                            </select>
                            @error('duration_hours')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Check Availability Button -->
                    @if($restaurant_id && $reservation_date && $reservation_time)
                        <div class="mt-4">
                            <button type="button" 
                                    @click="checkAvailability()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Check Availability
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Additional Guests Section -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Additional Guests</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Add names of people who will join you (optional)</p>
                        </div>
                        <button type="button" 
                                @click="addGuest()"
                                class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Guest
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach($guests as $index => $guest)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Guest {{ $index + 1 }}
                                    </h3>
                                    <button type="button" 
                                            @click="removeGuest({{ $index }})"
                                            class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Guest First Name -->
                                    <div>
                                        <label for="guest_name_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            First Name
                                        </label>
                                        <input type="text" 
                                               id="guest_name_{{ $index }}" 
                                               wire:model="guests.{{ $index }}.name"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Guest first name">
                                    </div>
                                    
                                    <!-- Guest Last Name -->
                                    <div>
                                        <label for="guest_surname_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Last Name
                                        </label>
                                        <input type="text" 
                                               id="guest_surname_{{ $index }}" 
                                               wire:model="guests.{{ $index }}.surname"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Guest last name">
                                    </div>

                                    <!-- Guest Email -->
                                    <div>
                                        <label for="guest_email_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Email <span class="text-gray-400 text-xs">(optional)</span>
                                        </label>
                                        <input type="email" 
                                               id="guest_email_{{ $index }}" 
                                               wire:model="guests.{{ $index }}.email"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="guest@example.com">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-center pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-medium text-lg rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-w-48">
                        <span wire:loading.remove>
                            Make Reservation
                        </span>
                        <span wire:loading class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating Reservation...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
