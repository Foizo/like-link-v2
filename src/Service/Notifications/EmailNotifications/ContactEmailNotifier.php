<?php declare(strict_types=1);
namespace App\Service\Notifications\EmailNotifications;

use App\Models\Contact\ContactRequest;
use Symfony\Component\Mime\Address;

class ContactEmailNotifier extends AbstractEmailNotifier
{
    const TEMPLATE_IDENTIFIER = 'contact-email';

    function getTemplateIdentifier(): string
    {
        return self::TEMPLATE_IDENTIFIER;
    }

    function sendContactNotification(ContactRequest $contact_request): bool
    {
        return $this->sendEmail(
            address: new Address($this->current_app->contact_email),
            subject: $contact_request->subject,
            context_parameters: [
                'from' => "{$contact_request->name} ({$contact_request->email})",
                'message' => $contact_request->message
            ]
        );
    }
}
