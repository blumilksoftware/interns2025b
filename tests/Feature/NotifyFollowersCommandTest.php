<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Interns2025b\Enums\EventStatus;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Interns2025b\Notifications\EventStartingSoonNotification;
use Interns2025b\Notifications\NewEventPublishedNotification;
use Tests\TestCase;

class NotifyFollowersCommandTest extends TestCase
{
    public function testSendsEventStartingSoonNotificationsToEventFollowers(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            "status" => EventStatus::Published,
            "start" => now()->addHours(3),
        ]);

        $user->followingEvents()->attach($event);

        $this->artisan("followers:notify")->assertSuccessful();

        Notification::assertSentTo($user, EventStartingSoonNotification::class);
    }

    public function testSendsNewEventPublishedNotificationToFollowersOfOrganization(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $organization->followers()->attach($user);

        $event = Event::factory()->create([
            "status" => EventStatus::Published,
            "created_at" => now(),
            "owner_type" => Organization::class,
            "owner_id" => $organization->id,
        ]);

        $this->artisan("followers:notify")->assertSuccessful();

        Notification::assertSentTo($user, NewEventPublishedNotification::class, fn($notification): bool => $notification->getEvent()->id === $event->id);
    }

    public function testSendsNewEventPublishedNotificationToFollowersOfUser(): void
    {
        $owner = User::factory()->create();
        $follower = User::factory()->create();
        $owner->followers()->attach($follower);

        $event = Event::factory()->create([
            "status" => EventStatus::Published,
            "created_at" => now(),
            "owner_type" => User::class,
            "owner_id" => $owner->id,
        ]);

        $this->artisan("followers:notify")->assertSuccessful();

        Notification::assertSentTo($follower, NewEventPublishedNotification::class, fn($notification): bool => $notification->getEvent()->id === $event->id);
    }
}
