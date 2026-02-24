<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ReminderDueNotification extends Notification
{
    use Queueable;

    protected $reminder;

    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    public function via($notifiable)
    {
        return ['database']; // Store in database only for now
    }

    public function toDatabase($notifiable)
    {
        return [
            'reminder_id' => $this->reminder->id,
            'title' => $this->reminder->title,
            'message' => $this->reminder->message,
            'remind_at' => $this->reminder->remind_at->format('Y-m-d H:i:s'),
            'type' => 'reminder_due'
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'reminder_id' => $this->reminder->id,
            'title' => $this->reminder->title,
            'message' => $this->reminder->message,
        ];
    }
}