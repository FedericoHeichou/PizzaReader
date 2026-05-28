<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../../app/Helpers/helper_functions.php';
    }

    // ── trimChar ──────────────────────────────────────────────────────────────

    public function test_trimChar_removes_leading_and_trailing_commas(): void
    {
        $this->assertSame('a,b,c', trimChar(',a,b,c,', ','));
    }

    public function test_trimChar_collapses_consecutive_commas(): void
    {
        $this->assertSame('a,b', trimChar('a,,b', ','));
    }

    public function test_trimChar_removes_spaces_around_delimiter(): void
    {
        $this->assertSame('a,b', trimChar('a , b', ','));
    }

    public function test_trimChar_newline_delimiter(): void
    {
        $this->assertSame("Title A\nTitle B", trimChar("\nTitle A\nTitle B\n", "\n"));
    }

    // ── getSmallThumbnail ─────────────────────────────────────────────────────

    public function test_getSmallThumbnail_returns_small_jpg_for_jpg(): void
    {
        $this->assertSame('cover-small.jpg', getSmallThumbnail('cover.jpg'));
    }

    public function test_getSmallThumbnail_returns_small_jpg_for_png(): void
    {
        $this->assertSame('image-small.jpg', getSmallThumbnail('image.png'));
    }

    public function test_getSmallThumbnail_handles_query_string(): void
    {
        $this->assertSame('thumb-small.jpg?v=123', getSmallThumbnail('thumb.jpg?v=123'));
    }

    public function test_getSmallThumbnail_returns_null_for_null(): void
    {
        $this->assertNull(getSmallThumbnail(null));
    }

    // ── strip_forbidden_chars ─────────────────────────────────────────────────

    public function test_strip_forbidden_chars_removes_percent(): void
    {
        $this->assertSame('test3dfile.jpg', strip_forbidden_chars('test%3dfile.jpg'));
    }

    public function test_strip_forbidden_chars_removes_hash(): void
    {
        $this->assertSame('file.jpg', strip_forbidden_chars('fi#le.jpg'));
    }

    public function test_strip_forbidden_chars_removes_question_mark(): void
    {
        // The function removes '?' characters only, not the rest of the string
        $this->assertSame('file.jpgextra', strip_forbidden_chars('file.jpg?extra'));
    }

    public function test_strip_forbidden_chars_leaves_clean_string_unchanged(): void
    {
        $this->assertSame('clean_file.png', strip_forbidden_chars('clean_file.png'));
    }

    // ── getFormFieldsForValidation ─────────────────────────────────────────────

    public function test_getFormFieldsForValidation_marks_required_fields(): void
    {
        $fields = [
            [
                'type' => 'input_text',
                'parameters' => ['field' => 'name', 'required' => 1],
                'values' => ['max:191'],
            ],
        ];
        $result = getFormFieldsForValidation($fields);
        $this->assertContains('required', $result['name']);
    }

    public function test_getFormFieldsForValidation_marks_optional_fields_nullable(): void
    {
        $fields = [
            [
                'type' => 'input_text',
                'parameters' => ['field' => 'author'],
                'values' => ['max:191'],
            ],
        ];
        $result = getFormFieldsForValidation($fields);
        $this->assertContains('nullable', $result['author']);
    }

    public function test_getFormFieldsForValidation_marks_prohibited_fields(): void
    {
        $fields = [
            [
                'type' => 'textarea',
                'parameters' => ['field' => 'custom_html', 'prohibited' => true],
                'values' => ['max:3000'],
            ],
        ];
        $result = getFormFieldsForValidation($fields);
        $this->assertContains('prohibited', $result['custom_html']);
    }

    // ── getFieldsFromRequest ──────────────────────────────────────────────────

    public function test_getFieldsFromRequest_extracts_present_values(): void
    {
        $form_fields = ['name' => ['max:191'], 'author' => ['max:191']];

        // Build a fake request-like object
        $request = new class {
            public string $name = 'Test';
            public ?string $author = null;
            public function has(string $key): bool { return in_array($key, ['name']); }
        };

        $result = getFieldsFromRequest($request, $form_fields);
        $this->assertSame('Test', $result['name']);
        $this->assertNull($result['author']);
    }
}
