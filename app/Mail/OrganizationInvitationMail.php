<?php

declare(strict_types=1);

namespace Interns2025b\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrganizationInvitationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $organizationName,
        public string $url
    ) {}

    public function build()
    {
        return $this->subject(__("organization.invitation_subject"))
            ->view("emails.organization-invitation");
    }
}
