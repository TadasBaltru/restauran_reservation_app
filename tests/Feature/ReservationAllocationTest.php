<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ReservationService;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationAllocationTest extends TestCase
{
    use RefreshDatabase;

    protected ReservationService $reservationService;
    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->reservationService = new ReservationService();
        
        // Create a test restaurant
        $this->restaurant = Restaurant::factory()->create([
            'name' => 'Test Restaurant'
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up any remaining data
        Reservation::query()->delete();
        Table::query()->delete();
        Restaurant::query()->delete();
        
        parent::tearDown();
    }

    /** @test */
    public function it_creates_reservation_with_automatic_table_allocation()
    {
        // Arrange: Create tables
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 1',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 2',
            'min_people_count' => 4,
            'max_people_count' => 6
        ]);

        // Prepare reservation data for 4 people (main person + 3 guests)
        $reservationData = [
            'restaurant_id' => $this->restaurant->id,
            'reservation_name' => 'John',
            'reservation_surname' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '123-456-7890',
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '19:00',
            'duration_hours' => 2,
            'guests' => [
                ['name' => 'Jane', 'surname' => 'Smith'],
                ['name' => 'Bob', 'surname' => 'Johnson'],
                ['name' => 'Alice', 'surname' => 'Brown']
            ]
        ];

        // Act: Create reservation
        $result = $this->reservationService->createReservation($reservationData);

        // Assert: Reservation should be created with table allocation
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('allocation', $result);
        $this->assertArrayHasKey('reservation', $result);
        
        $reservation = $result['reservation'];
        $allocation = $result['allocation'];

        // Verify reservation details
        $this->assertEquals($this->restaurant->id, $reservation->restaurant_id);
        $this->assertEquals('John', $reservation->reservation_name);
        $this->assertEquals(3, $reservation->guests->count());

        // Verify table allocation
        $this->assertEquals(4, $allocation['total_people_seated']);
        $this->assertEquals(1, count($allocation['tables'])); // Should use single table
        $this->assertEquals('perfect_single_match', $allocation['allocation_strategy']);
        
        // Verify tables are attached to reservation
        $this->assertEquals(1, $reservation->tables->count());
        $this->assertEquals($allocation['tables'][0]['table_id'], $reservation->tables->first()->id);
    }

    /** @test */
    public function it_fails_reservation_when_no_tables_available()
    {
        // Arrange: Create only small tables
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Small Table',
            'min_people_count' => 1,
            'max_people_count' => 2
        ]);

        // Prepare reservation data for large party
        $reservationData = [
            'restaurant_id' => $this->restaurant->id,
            'reservation_name' => 'John',
            'reservation_surname' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '123-456-7890',
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '19:00',
            'duration_hours' => 2,
            'guests' => [
                ['name' => 'Jane', 'surname' => 'Smith'],
                ['name' => 'Bob', 'surname' => 'Johnson'],
                ['name' => 'Alice', 'surname' => 'Brown'],
                ['name' => 'Charlie', 'surname' => 'Wilson'],
                ['name' => 'Diana', 'surname' => 'Davis']
            ]
        ];

        // Act: Attempt to create reservation
        $result = $this->reservationService->createReservation($reservationData);

        // Assert: Reservation should fail
        $this->assertFalse($result['success']);
        $this->assertEquals('table_allocation_failed', $result['error']);
        $this->assertStringContainsString('No available tables', $result['message']);
        
        // Verify no reservation was created
        $this->assertEquals(0, Reservation::count());
    }

    /** @test */
    public function it_respects_existing_reservations_when_allocating()
    {
        // Arrange: Create tables
        $table1 = Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 1',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        $table2 = Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table 2',
            'min_people_count' => 2,
            'max_people_count' => 4
        ]);

        // Create existing reservation on Table 1
        $existingReservation = Reservation::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'reservation_date' => Carbon::tomorrow()->setHour(19)->setMinute(0),
            'duration_hours' => 2
        ]);
        $existingReservation->tables()->attach($table1->id);

        // Prepare new reservation data for same time slot
        $reservationData = [
            'restaurant_id' => $this->restaurant->id,
            'reservation_name' => 'Jane',
            'reservation_surname' => 'Smith',
            'email' => 'jane@example.com',
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '19:30', // Overlaps with existing reservation
            'duration_hours' => 2,
            'guests' => [
                ['name' => 'John', 'surname' => 'Doe'],
                ['name' => 'Bob', 'surname' => 'Johnson']
            ]
        ];

        // Act: Create new reservation
        $result = $this->reservationService->createReservation($reservationData);

        // Assert: Should succeed using Table 2
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('allocation', $result);
        
        $reservation = $result['reservation'];
        $allocation = $result['allocation'];

        // Verify it used Table 2 (not the reserved Table 1)
        $this->assertEquals($table2->id, $allocation['tables'][0]['table_id']);
        $this->assertEquals($table2->id, $reservation->tables->first()->id);
        
        // Verify both reservations exist
        $this->assertEquals(2, Reservation::count());
    }

    /** @test */
    public function it_handles_reservation_with_no_guests()
    {
        // Arrange: Create table for single person
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Single Table',
            'min_people_count' => 1,
            'max_people_count' => 2
        ]);

        // Prepare reservation data with no guests
        $reservationData = [
            'restaurant_id' => $this->restaurant->id,
            'reservation_name' => 'John',
            'reservation_surname' => 'Doe',
            'email' => 'john@example.com',
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '19:00',
            'duration_hours' => 2,
            'guests' => [] // No guests
        ];

        // Act: Create reservation
        $result = $this->reservationService->createReservation($reservationData);

        // Assert: Should succeed for 1 person
        $this->assertTrue($result['success']);
        
        $allocation = $result['allocation'];
        $this->assertEquals(1, $allocation['total_people_seated']);
        $this->assertEquals(1, $allocation['total_wasted_seats']); // Table capacity 2, person 1
    }

    /** @test */
    public function it_provides_detailed_allocation_information_in_response()
    {
        // Arrange
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Perfect Table',
            'min_people_count' => 3,
            'max_people_count' => 3
        ]);

        $reservationData = [
            'restaurant_id' => $this->restaurant->id,
            'reservation_name' => 'John',
            'reservation_surname' => 'Doe',
            'email' => 'john@example.com',
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '19:00',
            'duration_hours' => 2,
            'guests' => [
                ['name' => 'Jane', 'surname' => 'Smith'],
                ['name' => 'Bob', 'surname' => 'Johnson']
            ]
        ];

        // Act
        $result = $this->reservationService->createReservation($reservationData);

        // Assert: Detailed allocation information should be present
        $this->assertTrue($result['success']);
        
        $allocation = $result['allocation'];
        $this->assertArrayHasKey('tables', $allocation);
        $this->assertArrayHasKey('total_people_seated', $allocation);
        $this->assertArrayHasKey('total_wasted_seats', $allocation);
        $this->assertArrayHasKey('allocation_strategy', $allocation);

        // Each table entry should have detailed information
        $tableAllocation = $allocation['tables'][0];
        $this->assertArrayHasKey('table_id', $tableAllocation);
        $this->assertArrayHasKey('table_name', $tableAllocation);
        $this->assertArrayHasKey('people_seated', $tableAllocation);
        $this->assertArrayHasKey('table_capacity', $tableAllocation);

        // Success message should include allocation summary
        $this->assertStringContainsString('perfect_single_match', $result['message']);
    }

    /** @test */
    public function it_handles_multiple_table_allocation_scenario()
    {
        // Arrange: Create smaller tables that need to be combined
        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table A',
            'min_people_count' => 2,
            'max_people_count' => 3
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table B',
            'min_people_count' => 2,
            'max_people_count' => 3
        ]);

        Table::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'Table C',
            'min_people_count' => 1,
            'max_people_count' => 2
        ]);

        // Prepare reservation for 7 people
        $reservationData = [
            'restaurant_id' => $this->restaurant->id,
            'reservation_name' => 'John',
            'reservation_surname' => 'Doe',
            'email' => 'john@example.com',
            'reservation_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reservation_time' => '19:00',
            'duration_hours' => 2,
            'guests' => [
                ['name' => 'Jane', 'surname' => 'Smith'],
                ['name' => 'Bob', 'surname' => 'Johnson'],
                ['name' => 'Alice', 'surname' => 'Brown'],
                ['name' => 'Charlie', 'surname' => 'Wilson'],
                ['name' => 'Diana', 'surname' => 'Davis']
            ]
        ];

        // Act
        $result = $this->reservationService->createReservation($reservationData);

        // Assert: Should succeed with multiple tables
        $this->assertTrue($result['success']);
        
        $reservation = $result['reservation'];
        $allocation = $result['allocation'];

        $this->assertEquals(6, $allocation['total_people_seated']); // 1 main + 5 guests
        $this->assertGreaterThan(1, count($allocation['tables'])); // Multiple tables
        $this->assertEquals('optimal_combination', $allocation['allocation_strategy']);
        
        // Verify all tables are attached to reservation
        $this->assertEquals(count($allocation['tables']), $reservation->tables->count());
        
        // Verify table allocation adds up correctly
        $totalSeated = array_sum(array_column($allocation['tables'], 'people_seated'));
        $this->assertEquals(6, $totalSeated);
    }
}
