<?php

namespace Tests\Feature;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CommentTest extends TestCase {
    public function test_create_comment() {
        $comment = new Comment();
        $comment->email = 'test@mail.com';
        $comment->title = 'Sample Title';
        $comment->comment = 'Sample Commet';

        $result = $comment->save();
        $this->assertTrue($result);

        // Log::info(json_encode($comment));
    }

    public function test_default_attribute_values() {
        $comment = new Comment();
        $comment->email = 'test@mail.com';

        $comment->save();
        $this->assertNotNull($comment->id);
        $this->assertNotNull($comment->title);
        $this->assertNotNull($comment->comment);
    }
}
