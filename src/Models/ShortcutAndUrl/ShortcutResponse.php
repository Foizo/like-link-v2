<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

use App\Doctrine\Entity\CustomerUrl;

class ShortcutResponse
{
    public ?CustomerUrl $customer_url = null;

    public ?string $destination_url = null;
}