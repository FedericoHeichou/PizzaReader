<?php

namespace Tests\Feature\Api;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the Reader API (api.php routes).
 * All routes are prefixed with /api/ and handled by ReaderController.
 */
class ReaderApiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    // ── /api/info ──────────────────────────────────────────────────────────────

    public function test_info_returns_json_with_socials_key(): void
    {
        $response = $this->getJson('/api/info');
        $response->assertStatus(200)
                 ->assertJsonStructure(['info' => ['socials']]);
    }

    public function test_info_socials_are_empty_when_none_set(): void
    {
        $response = $this->getJson('/api/info');
        $response->assertStatus(200)
                 ->assertJson(['info' => ['socials' => []]]);
    }

    public function test_info_includes_social_when_set(): void
    {
        Settings::where('key', 'social_facebook')->update(['value' => 'https://facebook.com/test']);

        $response = $this->getJson('/api/info');
        $response->assertStatus(200);
        $data = $response->json('info.socials');
        $this->assertNotEmpty($data);
        $this->assertSame('https://facebook.com/test', $data[0]['url']);
    }

    // ── /api/comics ────────────────────────────────────────────────────────────

    public function test_comics_returns_json_with_comics_key(): void
    {
        $response = $this->getJson('/api/comics');
        $response->assertStatus(200)
                 ->assertJsonStructure(['comics']);
    }

    public function test_comics_returns_empty_list_when_no_comics(): void
    {
        $response = $this->getJson('/api/comics');
        $response->assertStatus(200)
                 ->assertJson(['comics' => []]);
    }

    public function test_comics_returns_public_comics(): void
    {
        Comic::factory()->create(['hidden' => 0]);
        $response = $this->getJson('/api/comics');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('comics'));
    }

    public function test_comics_does_not_return_hidden_comics(): void
    {
        Comic::factory()->hidden()->create();
        $response = $this->getJson('/api/comics');
        $response->assertStatus(200)
                 ->assertJson(['comics' => []]);
    }

    public function test_comics_returned_fields_structure(): void
    {
        Comic::factory()->create(['hidden' => 0]);
        $response = $this->getJson('/api/comics');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'comics' => [
                         '*' => ['title', 'thumbnail', 'description', 'slug', 'url'],
                     ],
                 ]);
    }

    // ── /api/recommended ───────────────────────────────────────────────────────

    public function test_recommended_returns_json_with_comics_key(): void
    {
        $response = $this->getJson('/api/recommended');
        $response->assertStatus(200)
                 ->assertJsonStructure(['comics']);
    }

    public function test_recommended_returns_only_public_comics(): void
    {
        Comic::factory()->create(['hidden' => 0, 'order_index' => 1]);
        Comic::factory()->hidden()->create(['order_index' => 2]);

        $response = $this->getJson('/api/recommended');
        $this->assertCount(1, $response->json('comics'));
    }

    // ── /api/comics/{slug} ─────────────────────────────────────────────────────

    public function test_comic_returns_404_for_unknown_slug(): void
    {
        $response = $this->getJson('/api/comics/nonexistent-comic');
        $response->assertStatus(404);
    }

    public function test_comic_returns_200_for_existing_slug(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        $response = $this->getJson('/api/comics/' . $comic->slug);
        $response->assertStatus(200)
                 ->assertJsonStructure(['comic' => ['title', 'slug', 'chapters']]);
    }

    public function test_comic_returns_404_for_hidden_comic(): void
    {
        $comic = Comic::factory()->hidden()->create();
        $response = $this->getJson('/api/comics/' . $comic->slug);
        $response->assertStatus(404);
    }

    public function test_comic_returns_chapters(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        Chapter::factory()->create(['comic_id' => $comic->id, 'hidden' => 0]);

        $response = $this->getJson('/api/comics/' . $comic->slug);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('comic.chapters'));
    }

    public function test_comic_does_not_include_hidden_chapters(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        Chapter::factory()->hidden()->create(['comic_id' => $comic->id]);

        $response = $this->getJson('/api/comics/' . $comic->slug);
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('comic.chapters'));
    }

    // ── /api/search/{search} ───────────────────────────────────────────────────

    public function test_search_returns_json_with_comics_key(): void
    {
        $response = $this->getJson('/api/search/dragon');
        $response->assertStatus(200)
                 ->assertJsonStructure(['comics']);
    }

    public function test_search_finds_matching_comic_by_name(): void
    {
        Comic::factory()->create(['name' => 'Dragon Ball Z', 'hidden' => 0]);
        $response = $this->getJson('/api/search/Dragon');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('comics'));
    }

    public function test_search_returns_empty_for_no_match(): void
    {
        Comic::factory()->create(['name' => 'One Piece', 'hidden' => 0]);
        $response = $this->getJson('/api/search/Naruto');
        $response->assertStatus(200)
                 ->assertJson(['comics' => []]);
    }

    public function test_search_does_not_return_hidden_comics(): void
    {
        Comic::factory()->hidden()->create(['name' => 'Dragon Ball Hidden']);
        $response = $this->getJson('/api/search/Dragon');
        $response->assertStatus(200)
                 ->assertJson(['comics' => []]);
    }

    public function test_search_term_too_short_returns_empty(): void
    {
        Comic::factory()->create(['name' => 'Ab', 'hidden' => 0]);
        // Search terms shorter than 3 alphanumeric chars return no results per Comic::scopeSearch
        $response = $this->getJson('/api/search/ab');
        $response->assertStatus(200)
                 ->assertJson(['comics' => []]);
    }

    // ── /api/targets/{target} ──────────────────────────────────────────────────

    public function test_targets_returns_json_with_comics_key(): void
    {
        $response = $this->getJson('/api/targets/shonen');
        $response->assertStatus(200)
                 ->assertJsonStructure(['comics']);
    }

    public function test_targets_finds_matching_comics(): void
    {
        Comic::factory()->create(['target' => 'Shonen', 'hidden' => 0]);
        $response = $this->getJson('/api/targets/Shonen');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('comics'));
    }

    public function test_targets_returns_empty_for_no_match(): void
    {
        Comic::factory()->create(['target' => 'Seinen', 'hidden' => 0]);
        $response = $this->getJson('/api/targets/Shojo');
        $response->assertStatus(200)
                 ->assertJson(['comics' => []]);
    }

    // ── /api/genres/{genre} ────────────────────────────────────────────────────

    public function test_genres_returns_json_with_comics_key(): void
    {
        $response = $this->getJson('/api/genres/action');
        $response->assertStatus(200)
                 ->assertJsonStructure(['comics']);
    }

    public function test_genres_finds_matching_comics(): void
    {
        Comic::factory()->create(['genres' => 'Action, Adventure', 'hidden' => 0]);
        $response = $this->getJson('/api/genres/Action');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('comics'));
    }

    public function test_genres_returns_empty_for_no_match(): void
    {
        Comic::factory()->create(['genres' => 'Romance', 'hidden' => 0]);
        $response = $this->getJson('/api/genres/Action');
        $response->assertStatus(200)
                 ->assertJson(['comics' => []]);
    }

    // ── /api/read/{comic}/{language}/{ch?} ─────────────────────────────────────

    public function test_read_returns_404_for_nonexistent_comic(): void
    {
        $response = $this->getJson('/api/read/nonexistent/en/ch/1');
        $response->assertStatus(404);
    }

    public function test_read_returns_404_for_hidden_comic(): void
    {
        $comic = Comic::factory()->hidden()->create();
        $response = $this->getJson('/api/read/' . $comic->slug . '/en/ch/1');
        $response->assertStatus(404);
    }

    public function test_read_returns_404_for_nonexistent_chapter(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        $response = $this->getJson('/api/read/' . $comic->slug . '/en/ch/999');
        $response->assertStatus(404);
    }

    public function test_read_returns_200_for_valid_chapter(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        Chapter::factory()->create([
            'comic_id' => $comic->id,
            'language' => 'en',
            'chapter' => 1,
            'volume' => null,
            'subchapter' => null,
            'hidden' => 0,
        ]);

        $response = $this->getJson('/api/read/' . $comic->slug . '/en/ch/1');
        $response->assertStatus(200)
                 ->assertJsonStructure(['comic', 'chapter']);
    }

    public function test_read_returns_comic_and_chapter_structure(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        Chapter::factory()->create([
            'comic_id' => $comic->id,
            'language' => 'en',
            'chapter' => 5,
            'volume' => null,
            'subchapter' => null,
            'hidden' => 0,
        ]);

        $response = $this->getJson('/api/read/' . $comic->slug . '/en/ch/5');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'comic' => ['title', 'slug'],
                     'chapter' => ['pages', 'vote_id', 'previous', 'next'],
                 ]);
    }

    public function test_read_with_volume_and_chapter(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        Chapter::factory()->create([
            'comic_id' => $comic->id,
            'language' => 'en',
            'volume' => 1,
            'chapter' => 1,
            'subchapter' => null,
            'hidden' => 0,
        ]);

        $response = $this->getJson('/api/read/' . $comic->slug . '/en/vol/1/ch/1');
        $response->assertStatus(200);
        $this->assertNotNull($response->json('chapter'));
    }

    public function test_read_returns_null_chapter_for_invalid_ch_format(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        // Odd number of segments in ch → explodeCh returns null → 404
        $response = $this->getJson('/api/read/' . $comic->slug . '/en/ch');
        $response->assertStatus(404);
    }

    // ── /api/vote/{chapter_id} ─────────────────────────────────────────────────

    public function test_get_vote_returns_vote_structure(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        $chapter = Chapter::factory()->create(['comic_id' => $comic->id, 'hidden' => 0]);

        $response = $this->getJson('/api/vote/' . $chapter->id);
        $response->assertStatus(200)
                 ->assertJsonStructure(['vote_id', 'vote_token', 'your_vote']);
    }

    public function test_get_vote_returns_correct_chapter_id(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        $chapter = Chapter::factory()->create(['comic_id' => $comic->id, 'hidden' => 0]);

        $response = $this->getJson('/api/vote/' . $chapter->id);
        $response->assertStatus(200)
                 ->assertJsonPath('vote_id', (string) $chapter->id);
    }

    public function test_get_vote_your_vote_is_null_when_no_vote(): void
    {
        $comic = Comic::factory()->create(['hidden' => 0]);
        $chapter = Chapter::factory()->create(['comic_id' => $comic->id, 'hidden' => 0]);

        $response = $this->getJson('/api/vote/' . $chapter->id);
        $response->assertStatus(200)
                 ->assertJsonPath('your_vote', null);
    }
}
