<?php

declare(strict_types=1);

namespace Interns2025b\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Interns2025b\Models\Organization;

class OrganizationInvitationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Organization $organization
    ) {}

    public function build(): self
    {
        $url = URL::temporarySignedRoute(
            "organizations.accept-invite",
            now()->addHours(48),
            [
                "organization" => $this->organization->id,
                "email" => $this->to[0]["address"],
            ],
        );

        return $this->view("emails.organization-invitation")
            ->with([
                "organizationName" => $this->organization->name,
                "url" => $url,
            ]);
    }
}
