<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;
use App\Models\Notification;

class CheckDueBorrows extends Command
{
    protected $signature = 'borrows:check-due';
    protected $description = 'Check for due/overdue borrows and notify users';

    public function handle()
    {
        // Notify for items that are due now (within the last minute)
        $now = now();
        $oneMinuteAgo = now()->subMinute();

        $dueBorrows = Borrow::with('resource')
            ->whereIn('status', ['approved', 'borrowed'])
            ->whereBetween('due_at', [$oneMinuteAgo, $now])
            ->get();

        foreach ($dueBorrows as $borrow) {
            $alreadyNotified = Notification::where('user_id', $borrow->user_id)
                ->where('title', 'Item Due Now')
                ->where('message', 'LIKE', "%{$borrow->resource->name}%")
                ->where('created_at', '>=', now()->subMinutes(30))
                ->exists();

            if (!$alreadyNotified) {
                Notification::create([
                    'user_id' => $borrow->user_id,
                    'title' => 'Item Due Now',
                    'message' => "{$borrow->quantity}x {$borrow->resource->name} is now due. Please return it as soon as possible.",
                ]);
                $this->info("Notified user {$borrow->user_id} about due item: {$borrow->resource->name}");
            }
        }

        // Remind for items due within 30 minutes (once per item per hour)
        $thirtyMinFromNow = now()->addMinutes(30);
        $nowTime = now();

        $upcomingBorrows = Borrow::with('resource', 'user')
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_at', '<=', $thirtyMinFromNow)
            ->where('due_at', '>', $nowTime)
            ->get();

        foreach ($upcomingBorrows as $borrow) {
            $alreadyNotified = Notification::where('user_id', $borrow->user_id)
                ->where('title', 'Item Due Soon')
                ->where('message', 'LIKE', "%{$borrow->resource->name}%")
                ->where('created_at', '>=', now()->subHours(1))
                ->exists();

            if (!$alreadyNotified) {
                $minsLeft = round(now()->diffInMinutes($borrow->due_at));
                Notification::create([
                    'user_id' => $borrow->user_id,
                    'title' => 'Item Due Soon',
                    'message' => "{$borrow->quantity}x {$borrow->resource->name} is due in {$minsLeft} minutes. Please prepare to return it.",
                ]);
                $this->info("Reminded user {$borrow->user_id} about upcoming due: {$borrow->resource->name} in {$minsLeft}min");
            }
        }

        // Alert for overdue items (once per item per 2 hours)
        $overdueBorrows = Borrow::with('resource', 'user')
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_at', '<', now())
            ->get();

        foreach ($overdueBorrows as $borrow) {
            $alreadyNotified = Notification::where('user_id', $borrow->user_id)
                ->where('title', '⚠ Overdue Item')
                ->where('message', 'LIKE', "%{$borrow->resource->name}%")
                ->where('created_at', '>=', now()->subHours(2))
                ->exists();

            if (!$alreadyNotified) {
                $hoursOverdue = max(1, round(abs(now()->diffInHours($borrow->due_at))));
                Notification::create([
                    'user_id' => $borrow->user_id,
                    'title' => '⚠ Overdue Item',
                    'message' => "{$borrow->quantity}x {$borrow->resource->name} is overdue by {$hoursOverdue} hour(s). Please return it immediately.",
                ]);
                $this->info("Notified user {$borrow->user_id} about OVERDUE item: {$borrow->resource->name} (overdue by {$hoursOverdue}h)");
            }
        }

        $this->info('Due borrows check completed.');
        return Command::SUCCESS;
    }
}
