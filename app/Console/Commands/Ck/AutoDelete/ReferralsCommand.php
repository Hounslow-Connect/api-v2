<?php

namespace App\Console\Commands\Ck\AutoDelete;

use App\Models\Referral;
use Illuminate\Console\Command;

class ReferralsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:auto-delete:referrals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes any referrals that are due for deletion';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $months = Referral::AUTO_DELETE_MONTHS;

        $this->line("Deleting referrals completed {$months} month(s) ago...");
        $count = Referral::dueForDeletion()->delete();
        $this->info("Deleted {$count} referral(s).");
    }
}
