<?php

namespace Tests\Unit\Console\Commands\Ck\AutoDelete;

use App\Console\Commands\Ck\AutoDelete\PageFeedbacksCommand;
use App\Models\PageFeedback;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class PageFeedbacksTest extends TestCase
{
    public function test_auto_delete_works(): void
    {
        $newPageFeedback = PageFeedback::factory()->create([
            'created_at' => Date::today(),
            'updated_at' => Date::today(),
        ]);

        $dueForDeletionPageFeedback = PageFeedback::factory()->create([
            'created_at' => Date::today()->subMonths(PageFeedback::AUTO_DELETE_MONTHS),
            'updated_at' => Date::today()->subMonths(PageFeedback::AUTO_DELETE_MONTHS),
        ]);

        Artisan::call(PageFeedbacksCommand::class);

        $this->assertDatabaseHas($newPageFeedback->getTable(), ['id' => $newPageFeedback->id]);
        $this->assertDatabaseMissing($dueForDeletionPageFeedback->getTable(),
            ['id' => $dueForDeletionPageFeedback->id]);
    }
}
