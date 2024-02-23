<?php
namespace App\Form\Handler;

use App\Enums\ShortUrl;
use App\Form\ShortUrlForm;
use App\Models\ShortcutAndUrl\ShortUrlRequest;
use App\Models\ShortcutAndUrl\ShortUrlResponse;
use App\Service\ShortcutAndUrlManager\ShortUrlManager;
use Exception;
use Symfony\Component\Form\FormInterface;

class ShortUrlFormHandler
{
    function __construct(
        protected ShortUrlManager $short_url_manager
    ){}

    function handleForm(FormInterface $form): ShortUrlResponse
    {
        if (!$form->isValid()) {
            $short_url_response = new ShortUrlResponse();
            $short_url_response->valid_response = false;
            $short_url_response->errors = $this->getFormErrorMessages($form);
            return $short_url_response;
        }

        return $this->handleValidForm($form);
    }

    /** @throws Exception */
    protected function handleValidForm(FormInterface $form): ShortUrlResponse
    {
        $short_url_request = $form->getData();

        if (!$short_url_request instanceof ShortUrlRequest) {
            throw new Exception('Form handler use different data model');
        }

        $short_url_request->createDestinationUrlHash();
        if ($short_url_request->shortcut) {
            $short_url_request->customer_shortcut = true;
        }

        if (in_array($short_url_request->shortcut, ShortUrl::BLOCKED_CUSTOMER_SHORTCUTS)) {
            $short_url_response = new ShortUrlResponse();
            $short_url_response->valid_response = false;
            $short_url_response->errors = [ShortUrlForm::SHORTCUT_CHILD_NAME => 'Toto ne'];
            return $short_url_response;
        }

        return $this->short_url_manager->shortUrl($short_url_request);
    }

    private function getFormErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getFormErrorMessages($child);
            }
        }

        return $errors;
    }
}