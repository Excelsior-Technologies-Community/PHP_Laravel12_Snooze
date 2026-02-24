<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckReminders extends Command
{
    protected $signature = 'reminder:check';
    protected $description = 'Check and process due reminders';

    public function handle()
    {
        // Get reminders that are due (remind_at <= now)
        $dueReminders = Reminder::where('remind_at', '<=', Carbon::now())
            ->get();

        $this->info('Found ' . $dueReminders->count() . ' due reminders.');

        foreach ($dueReminders as $reminder) {
            // For now, we'll just log it since we don't have users yet
            $this->line("   🔔 Reminder due: {$reminder->title}");
            
            // Log the due reminder
            \Log::info("Reminder due", [
                'reminder_id' => $reminder->id,
                'title' => $reminder->title,
                'remind_at' => $reminder->remind_at
            ]);

            // Optional: You can add a flash message system
            session()->flash('notification_' . $reminder->id, [
                'title' => $reminder->title,
                'message' => $reminder->message
            ]);
        }

        if ($dueReminders->isEmpty()) {
            $this->info('No due reminders found.');
        }

        return Command::SUCCESS;
    }
}