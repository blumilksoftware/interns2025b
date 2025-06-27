<?php

declare(strict_types=1);

namespace Tests\Traits;

use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;

trait MocksFacebookUser
{
    protected function mockSocialiteUser(array $overrides = []): void
    {
        $user = \Mockery::mock(User::class);

        $email = array_key_exists("email", $overrides) ? $overrides["email"] : "test@example.com";
        $id = array_key_exists("id", $overrides) ? $overrides["id"] : "1234567890";
        $name = array_key_exists("name", $overrides) ? $overrides["name"] : "Test User";

        $user->shouldReceive("getEmail")->andReturn($email);
        $user->shouldReceive("getId")->andReturn($id);
        $user->shouldReceive("getName")->andReturn($name);

        $socialiteDriver = \Mockery::mock();
        $socialiteDriver->shouldReceive("stateless")->andReturnSelf();
        $socialiteDriver->shouldReceive("user")->andReturn($user);

        Socialite::shouldReceive("driver")
            ->with("facebook")
            ->andReturn($socialiteDriver);
    }
}
