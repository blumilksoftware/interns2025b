<?php

declare(strict_types=1);

namespace Interns2025b\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteAccountLinkMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;
    public $url;

    public function __construct($user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject(__("profile.confirm_account_deletion_subject"))
            ->view("emails.confirm-delete");
    }
}
