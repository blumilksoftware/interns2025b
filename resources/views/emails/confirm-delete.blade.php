<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('profile.email_deletion_requested') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 40px; margin: 0;">
<table style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
    <tr>
        <td style="padding: 40px; text-align: center; color: white;">
            <img src="{{ asset('images/LogoBrand.png') }}" alt="LetsEvent" style="max-height: 60px; margin-bottom: 16px;">
            <h1 style="margin: 0; font-size: 26px; font-weight: bold;">
                {{ __('profile.email_hello', ['name' => $user->name]) }}
            </h1>
        </td>
    </tr>

    <tr>
        <td style="padding: 40px; text-align: center; color: #374151;">
            <p style="font-size: 16px; margin-bottom: 24px;">
                {{ __('profile.email_deletion_requested') }}
            </p>

            <a href="{{ $url }}"
               style="background-color: #e3342f; color: #ffffff; padding: 14px 32px; border-radius: 9999px; font-weight: 600; text-decoration: none; display: inline-block; box-shadow: 0 2px 6px rgba(0,0,0,0.15); margin-bottom: 24px;">
                {{ __('profile.email_delete_button') }}
            </a>

            <p style="font-size: 14px; color: #6b7280; margin-top: 24px;">
                {{ __('profile.email_deletion_warning') }}
            </p>

            <p style="font-size: 14px; color: #6b7280; margin-top: 24px;">
                {{ __('profile.email_signature') }},<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
        </td>
    </tr>
</table>
</body>
</html>
