<?php

namespace Tests\Feature;

use App\Filament\Resources\ArticleResource\Pages\EditArticle;
use App\Models\Article;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleUploadPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_cover_uses_a_same_origin_preview_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('articles/covers/test-cover.jpg', 'test-image-content');

        $admin = User::factory()->create(['is_admin' => true]);
        $article = Article::create([
            'title' => 'Artikel mit Titelbild',
            'slug' => 'artikel-mit-titelbild',
            'body' => '<p>Inhalt</p>',
            'cover_image_path' => 'articles/covers/test-cover.jpg',
            'is_published' => true,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $form = Livewire::test(EditArticle::class, ['record' => $article->getRouteKey()])
            ->instance()
            ->form;
        $component = collect($form->getFlatComponents())
            ->first(fn ($component): bool => method_exists($component, 'getName')
                && $component->getName() === 'cover_image_path');
        $uploadedFile = collect($component->getUploadedFiles())->first();

        $this->assertSame('/storage/articles/covers/test-cover.jpg', $uploadedFile['url']);
        $this->assertSame('test-cover.jpg', $uploadedFile['name']);
    }
}
