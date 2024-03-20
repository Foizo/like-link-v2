<?php declare(strict_types=1);
namespace App\Models\Contact;

use App\Enums\Contact;
use Symfony\Component\Validator\Constraints as Assert;

class ContactRequest
{
    #[Assert\Length(max: Contact::NAME_COLUMN_LENGTH)]
    #[Assert\NotBlank]
    public string $name;

    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Assert\Length(max: Contact::SUBJECT_COLUMN_LENGTH)]
    #[Assert\NotBlank]
    public string $subject;

    #[Assert\Length(max: Contact::MESSAGE_COLUMN_LENGTH)]
    #[Assert\NotBlank]
    public string $message;
}