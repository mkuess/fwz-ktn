<?php

namespace Tests\Feature;

use Tests\TestCase;

class ArticleAttachmentUploadLimitTest extends TestCase
{
    public function test_livewire_allows_article_attachments_up_to_one_hundred_megabytes(): void
    {
        $this->assertContains('max:102400', config('livewire.temporary_file_upload.rules'));
        $this->assertSame(15, config('livewire.temporary_file_upload.max_upload_time'));
    }
}
