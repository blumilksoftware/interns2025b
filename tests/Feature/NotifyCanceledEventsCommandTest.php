<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;
use Interns2025b\Notifications\EventCanceledNotification;
use Tests\TestCase;

class NotifyCanceledEventsCommandTest extends TestCase
{
    public function testSendsEventCanceledNotificationToParticipants(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            "status" => "canceled",
            "updated_at" => now(),
        ]);

        $event->participants()->attach($user);

        $this->artisan("followers:notify-canceled")->assertSuccessful();

        Notification::assertSentTo(
            $user,
            EventCanceledNotification::class,
            fn($notification) => $notification->getEvent()->id === $event->id,
        );
    }
}
