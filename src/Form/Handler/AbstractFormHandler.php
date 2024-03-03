<?php
namespace App\Form\Handler;

use Symfony\Component\Form\FormInterface;

abstract class AbstractFormHandler
{
    abstract function handleForm(FormInterface $form);

    protected function getFormErrorMessages(FormInterface $form): array
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