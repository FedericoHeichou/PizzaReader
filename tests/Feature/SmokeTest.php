<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase {
    use RefreshDatabase;
    protected $seed = true;

    public function test_database_and_seeder_work() {
        $this->assertDatabaseCount('roles', 5);
        $this->assertDatabaseCount('comic_formats', 2);
    }

    public function test_home_returns_200() {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
