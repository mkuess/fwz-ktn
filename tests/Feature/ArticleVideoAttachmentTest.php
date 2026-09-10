<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleVideoAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_attachment_is_embedded_below_article_text(): void
    {
        $article = Article::create([
            'title' => 'Beitrag mit Video',
            'slug' => 'beitrag-mit-video',
            'body' => '<p>Text vor dem Video.</p>',
            'published_at' => now(),
            'is_published' => true,
        ]);

        $article->attachments()->create([
            'file_path' => 'articles/attachments/beitrag.mp4',
            'original_name' => 'Unser Beitrag.mp4',
            'mime_type' => 'video/mp4',
            'file_size' => 123456,
        ]);

        $article->attachments()->create([
            'file_path' => 'articles/attachments/information.pdf',
            'original_name' => 'Information.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 12345,
        ]);

        $this->get(route('articles.show', $article->slug))
            ->assertOk()
            ->assertSee('Text vor dem Video.')
            ->assertSee('<video', false)
            ->assertSee('articles/attachments/beitrag.mp4')
            ->assertSee('Unser Beitrag.mp4')
            ->assertSee('Information.pdf');
    }
}
