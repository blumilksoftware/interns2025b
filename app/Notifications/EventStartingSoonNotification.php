<?php

declare(strict_types=1);

namespace Interns2025b\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Interns2025b\Models\Event;

class EventStartingSoonNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Event $event,
    ) {}

    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__("events.event_starting_soon_subject", ["title" => $this->event->title]))
            ->line(__("events.event_starting_soon_line", [
                "start" => $this->event->start->toDayDateTimeString(),
            ]))
            ->action(__("events.see_event"), url("/events/{$this->event->id}"));
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function toArray(object $notifiable): array
    {
        return [
            "event_id" => $this->event->id,
            "title" => $this->event->title,
            "start" => $this->event->start,
        ];
    }
}
