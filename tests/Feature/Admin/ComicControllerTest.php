<?php

namespace Tests\Feature\Admin;

use App\Models\Comic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for Admin\ComicController routes under /admin/comics/*.
 *
 * Role hierarchy: admin=1, manager=2, editor=3, checker=4, user=5.
 * - index  : checker+
 * - create/store/edit/update/destroy/search : manager+
 * - show/stats : can.see (manager OR assigned checker+)
 */
class ComicControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    // ── index ─────────────────────────────────────────────────────────────────

    public function test_checker_can_see_comics_index(): void
    {
        $user = User::factory()->checker()->create();

        $this->actingAs($user)->get('/admin/comics')
            ->assertStatus(200);
    }

    public function test_regular_user_cannot_see_comics_index(): void
    {
        $user = User::factory()->create(); // role=5 (user)

        $this->actingAs($user)->get('/admin/comics')
            ->assertStatus(403);
    }

    public function test_guest_is_redirected_from_comics_index(): void
    {
        $this->get('/admin/comics')
            ->assertRedirect('/admin/login');
    }

    // ── create ────────────────────────────────────────────────────────────────

    public function test_manager_can_access_comic_create_form(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user)->get('/admin/comics/create')
            ->assertStatus(200);
    }

    public function test_editor_cannot_access_comic_create_form(): void
    {
        $user = User::factory()->editor()->create();

        $this->actingAs($user)->get('/admin/comics/create')
            ->assertStatus(403);
    }

    // ── store ─────────────────────────────────────────────────────────────────

    public function test_manager_can_create_a_comic(): void
    {
        $user = User::factory()->manager()->create();

        $response = $this->actingAs($user)->post('/admin/comics', [
            'name'            => 'Test Comic',
            'hidden'          => '0',
            'adult'           => '0',
            'order_index'     => '0',
            'comic_format_id' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comics', ['name' => 'Test Comic']);
    }

    public function test_create_comic_fails_without_name(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user)->post('/admin/comics', [
            'hidden'          => '0',
            'adult'           => '0',
            'order_index'     => '0',
            'comic_format_id' => '1',
        ])->assertSessionHasErrors('name');
    }

    // ── show ──────────────────────────────────────────────────────────────────

    public function test_manager_can_see_comic_show_page(): void
    {
        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();

        $this->actingAs($manager)->get("/admin/comics/{$comic->slug}")
            ->assertStatus(200);
    }

    public function test_checker_with_access_can_see_comic_show_page(): void
    {
        $checker = User::factory()->checker()->create();
        $comic   = Comic::factory()->create();
        $checker->comics()->attach($comic->id);

        $this->actingAs($checker)->get("/admin/comics/{$comic->slug}")
            ->assertStatus(200);
    }

    public function test_checker_without_access_cannot_see_comic_show_page(): void
    {
        $checker = User::factory()->checker()->create();
        $comic   = Comic::factory()->create();

        $this->actingAs($checker)->get("/admin/comics/{$comic->slug}")
            ->assertStatus(403);
    }

    public function test_show_returns_404_for_nonexistent_comic(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->get('/admin/comics/nonexistent-slug')
            ->assertStatus(404);
    }

    // ── edit ──────────────────────────────────────────────────────────────────

    public function test_manager_can_access_comic_edit_form(): void
    {
        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();

        $this->actingAs($manager)->get("/admin/comics/{$comic->slug}/edit")
            ->assertStatus(200);
    }

    public function test_editor_cannot_access_comic_edit_form(): void
    {
        $editor = User::factory()->editor()->create();
        $comic  = Comic::factory()->create();

        $this->actingAs($editor)->get("/admin/comics/{$comic->slug}/edit")
            ->assertStatus(403);
    }

    // ── update ────────────────────────────────────────────────────────────────

    public function test_manager_can_update_a_comic(): void
    {
        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();

        $this->actingAs($manager)->patch("/admin/comics/{$comic->id}", [
            'name'            => 'Updated Name',
            'hidden'          => '0',
            'adult'           => '0',
            'order_index'     => '0',
            'comic_format_id' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('comics', ['id' => $comic->id, 'name' => 'Updated Name']);
    }

    public function test_update_returns_404_for_nonexistent_comic(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->patch('/admin/comics/99999', [
            'name'   => 'X',
            'hidden' => '0',
        ])->assertStatus(404);
    }

    // ── destroy ───────────────────────────────────────────────────────────────

    public function test_manager_can_delete_a_comic(): void
    {
        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();

        $this->actingAs($manager)->delete("/admin/comics/{$comic->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('comics', ['id' => $comic->id]);
    }

    public function test_editor_cannot_delete_a_comic(): void
    {
        $editor = User::factory()->editor()->create();
        $comic  = Comic::factory()->create();

        $this->actingAs($editor)->delete("/admin/comics/{$comic->id}")
            ->assertStatus(403);
    }

    // ── search ────────────────────────────────────────────────────────────────

    public function test_manager_can_search_comics(): void
    {
        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create(['name' => 'Naruto']);

        $this->actingAs($manager)
            ->post("/admin/comics/search/Naruto")
            ->assertStatus(200);
    }

    // ── stats ─────────────────────────────────────────────────────────────────

    public function test_manager_can_view_comic_stats(): void
    {
        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();

        $this->actingAs($manager)->get("/admin/comics/{$comic->slug}/stats")
            ->assertStatus(200);
    }
}
