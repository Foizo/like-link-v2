<?php
namespace App\Form\Handler;

use App\Models\Contact\ContactResponse;
use Symfony\Component\Form\FormInterface;

class ContactFormHandler extends AbstractFormHandler
{
    function handleForm(FormInterface $form): ContactResponse
    {

        $contact_response = new ContactResponse();

        if (!$form->isValid()) {
            $contact_response->valid_response = false;
            $contact_response->errors = $this->getFormErrorMessages($form);
            return $contact_response;
        }

        return $contact_response;
    }
}