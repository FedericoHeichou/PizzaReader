<?php

namespace Tests\Feature\Admin;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tests for Admin\ChapterController routes under /admin/comics/{comic}/chapters/*.
 *
 * Role access: can.see = manager OR assigned checker+
 *              can.edit = manager OR assigned editor+
 *              destroy  = manager+
 */
class ChapterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    // ── index ─────────────────────────────────────────────────────────────────

    public function test_chapters_index_redirects_to_comic_show(): void
    {
        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();

        $this->actingAs($manager)
            ->get("/admin/comics/{$comic->slug}/chapters")
            ->assertRedirect("/admin/comics/{$comic->slug}");
    }

    // ── create ────────────────────────────────────────────────────────────────

    public function test_editor_with_access_can_see_chapter_create_form(): void
    {
        $editor = User::factory()->editor()->create();
        $comic  = Comic::factory()->create();
        $editor->comics()->attach($comic->id);

        $this->actingAs($editor)
            ->get("/admin/comics/{$comic->slug}/chapters/create")
            ->assertStatus(200);
    }

    public function test_checker_with_access_cannot_see_chapter_create_form(): void
    {
        $checker = User::factory()->checker()->create();
        $comic   = Comic::factory()->create();
        $checker->comics()->attach($comic->id);

        $this->actingAs($checker)
            ->get("/admin/comics/{$comic->slug}/chapters/create")
            ->assertStatus(403);
    }

    // ── store ─────────────────────────────────────────────────────────────────

    public function test_manager_can_create_a_chapter(): void
    {
        Storage::fake('local');

        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();

        $now = now()->format('Y-m-d\TH:i');

        $response = $this->actingAs($manager)->post("/admin/comics/{$comic->slug}/chapters", [
            'hidden'       => '0',
            'licensed'     => '0',
            'chapter'      => '1',
            'language'     => 'en',
            'published_on' => $now,
            'publish_start'=> $now,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('chapters', ['comic_id' => $comic->id, 'chapter' => 1]);
    }

    public function test_create_chapter_fails_without_language(): void
    {
        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();

        $now = now()->format('Y-m-d\TH:i');

        $this->actingAs($manager)->post("/admin/comics/{$comic->slug}/chapters", [
            'hidden'       => '0',
            'licensed'     => '0',
            'chapter'      => '1',
            'published_on' => $now,
            'publish_start'=> $now,
        ])->assertSessionHasErrors('language');
    }

    // ── show ──────────────────────────────────────────────────────────────────

    public function test_checker_with_access_can_see_chapter_show(): void
    {
        $checker = User::factory()->checker()->create();
        $comic   = Comic::factory()->create();
        $chapter = Chapter::factory()->for($comic)->create();
        $checker->comics()->attach($comic->id);

        $this->actingAs($checker)
            ->get("/admin/comics/{$comic->slug}/chapters/{$chapter->id}")
            ->assertStatus(200);
    }

    public function test_chapter_show_returns_404_for_wrong_comic(): void
    {
        $manager  = User::factory()->manager()->create();
        $comic1   = Comic::factory()->create();
        $comic2   = Comic::factory()->create();
        $chapter  = Chapter::factory()->for($comic1)->create();

        $this->actingAs($manager)
            ->get("/admin/comics/{$comic2->slug}/chapters/{$chapter->id}")
            ->assertStatus(404);
    }

    // ── edit ──────────────────────────────────────────────────────────────────

    public function test_editor_with_access_can_see_chapter_edit_form(): void
    {
        $editor  = User::factory()->editor()->create();
        $comic   = Comic::factory()->create();
        $chapter = Chapter::factory()->for($comic)->create();
        $editor->comics()->attach($comic->id);

        $this->actingAs($editor)
            ->get("/admin/comics/{$comic->slug}/chapters/{$chapter->id}/edit")
            ->assertStatus(200);
    }

    // ── update ────────────────────────────────────────────────────────────────

    public function test_manager_can_update_a_chapter(): void
    {
        Storage::fake('local');

        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();
        $chapter = Chapter::factory()->for($comic)->create(['chapter' => 5]);

        $now = now()->format('Y-m-d\TH:i');

        $this->actingAs($manager)->patch("/admin/comics/{$comic->id}/chapters/{$chapter->id}", [
            'hidden'       => '0',
            'licensed'     => '0',
            'chapter'      => '10',
            'language'     => 'en',
            'published_on' => $now,
            'publish_start'=> $now,
        ])->assertRedirect();

        $this->assertDatabaseHas('chapters', ['id' => $chapter->id, 'chapter' => 10]);
    }

    // ── destroy ───────────────────────────────────────────────────────────────

    public function test_manager_can_delete_a_chapter(): void
    {
        Storage::fake('local');

        $manager = User::factory()->manager()->create();
        $comic   = Comic::factory()->create();
        $chapter = Chapter::factory()->for($comic)->create();

        $this->actingAs($manager)
            ->delete("/admin/comics/{$comic->slug}/chapters/{$chapter->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('chapters', ['id' => $chapter->id]);
    }

    public function test_editor_cannot_delete_a_chapter(): void
    {
        $editor  = User::factory()->editor()->create();
        $comic   = Comic::factory()->create();
        $chapter = Chapter::factory()->for($comic)->create();
        $editor->comics()->attach($comic->id);

        $this->actingAs($editor)
            ->delete("/admin/comics/{$comic->slug}/chapters/{$chapter->id}")
            ->assertStatus(403);
    }
}
