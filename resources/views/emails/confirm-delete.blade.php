<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('profile.email_deletion_requested') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background: white; border-radius: 6px; padding: 20px;">
    <tr>
        <td>
            <h2>{{ __('profile.email_hello', ['name' => $user->name]) }}</h2>

            <p>{{ __('profile.email_deletion_requested') }}</p>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $url }}"
                   style="background-color: #e3342f; color: white; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                    {{ __('profile.email_delete_button') }}
                </a>
            </p>

            <p>{{ __('profile.email_deletion_warning') }}</p>

            <p>{{ __('profile.email_signature') }},<br>{{ config('app.name') }}</p>
        </td>
    </tr>
</table>
</body>
</html>
