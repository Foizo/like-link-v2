<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

use App\Enums\Shortcut\CustomerShortcut;
use App\Enums\ShortUrl;
use Symfony\Component\Validator\Constraints as Assert;

class ShortUrlRequest
{
    #[Assert\Regex(ShortUrl::URL_PATTERN)]
    #[Assert\NotBlank]
    public string $destination_url;

    #[Assert\Regex(CustomerShortcut::SHORTCUT_PATTERN)]
    #[Assert\Length(max: CustomerShortcut::ENTITY_COLUMN_LENGTH)]
    public ?string $shortcut = null;

    public string $destination_url_md5_hash;

    public bool $customer_shortcut = false;

    public bool $valid_request = true;


    function createDestinationUrlHash(): void
    {
        $this->destination_url_md5_hash = md5($this->destination_url);
    }
}