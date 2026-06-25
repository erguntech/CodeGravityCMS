<?php

namespace App\Console\Commands;

use App\Models\StorePurchase;
use Illuminate\Console\Command;

class ExpirePurchases extends Command
{
    protected $signature = 'store:expire-purchases';
    protected $description = 'Süresi dolan mağaza satın alımlarını pasif hale getirir.';

    public function handle()
    {
        $expiredCount = StorePurchase::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("{$expiredCount} adet süresi dolan satın alım pasif hale getirildi.");
        
        return Command::SUCCESS;
    }
}
