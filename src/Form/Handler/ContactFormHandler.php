<?php
namespace App\Form\Handler;

use App\Models\Contact\ContactResponse;
use App\Service\Notifications\EmailNotifications\ContactEmailNotifier;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactFormHandler extends AbstractFormHandler
{
    function __construct(
      private ContactEmailNotifier $email_notifier,
      protected TranslatorInterface $translator
    ){}

    function handleForm(FormInterface $form): ContactResponse
    {

        $contact_response = new ContactResponse();

        if (!$form->isValid()) {
            $contact_response->valid_response = false;
            $contact_response->errors = $this->getFormErrorMessages($form);
            return $contact_response;
        }

        if ($this->email_notifier->sendContactNotification($form->getData())) {
            return $contact_response;
        }

        $contact_response->valid_response = false;
        $contact_response->errors = ['submit' => $this->translator->trans('contact.unexpected_error', domain: 'errors')];

        return $contact_response;
    }
}