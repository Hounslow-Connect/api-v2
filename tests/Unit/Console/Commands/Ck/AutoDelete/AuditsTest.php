<?php

namespace Tests\Unit\Console\Commands\Ck\AutoDelete;

use App\Console\Commands\Ck\AutoDelete\AuditsCommand;
use App\Models\Audit;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class AuditsTest extends TestCase
{
    public function test_auto_delete_works(): void
    {
        $newAudit = Audit::factory()->create([
            'created_at' => Date::today(),
            'updated_at' => Date::today(),
        ]);

        $dueForDeletionAudit = Audit::factory()->create([
            'created_at' => Date::today()->subMonths(Audit::AUTO_DELETE_MONTHS),
            'updated_at' => Date::today()->subMonths(Audit::AUTO_DELETE_MONTHS),
        ]);

        Artisan::call(AuditsCommand::class);

        $this->assertDatabaseHas($newAudit->getTable(), ['id' => $newAudit->id]);
        $this->assertDatabaseMissing($dueForDeletionAudit->getTable(), ['id' => $dueForDeletionAudit->id]);
    }
}
