<?php

namespace Tests\Unit;

use App\Models\Restaurant;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    public function it_has_fillable_attributes()
    {
        $restaurant = new Restaurant();
        $fillable = $restaurant->getFillable();

        $this->assertContains('name', $fillable);
    }

    public function it_can_create_a_restaurant()
    {
        $restaurant = Restaurant::create([
            'name' => 'Test Restaurant'
        ]);

        $this->assertInstanceOf(Restaurant::class, $restaurant);
        $this->assertEquals('Test Restaurant', $restaurant->name);
        $this->assertDatabaseHas('restaurants', [
            'name' => 'Test Restaurant'
        ]);
    }


    public function it_can_have_multiple_tables()
    {
        $restaurant = Restaurant::factory()->create();
        
        Table::factory()->count(3)->create([
            'restaurant_id' => $restaurant->id
        ]);

        $this->assertCount(3, $restaurant->fresh()->tables);
    }

    public function it_calculates_total_tables_attribute()
    {
        $restaurant = Restaurant::factory()->create();
        
        Table::factory()->count(5)->create([
            'restaurant_id' => $restaurant->id
        ]);

        $this->assertEquals(5, $restaurant->fresh()->total_tables);
    }


    public function it_deletes_associated_tables_when_restaurant_is_deleted()
    {
        $restaurant = Restaurant::factory()->create();
        
        $table1 = Table::factory()->create(['restaurant_id' => $restaurant->id]);
        $table2 = Table::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertDatabaseHas('tables', ['id' => $table1->id]);
        $this->assertDatabaseHas('tables', ['id' => $table2->id]);

        $restaurant->delete();

        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
    }


}

