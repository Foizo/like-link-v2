<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Entity\ShortcutUrl;

abstract class AbstractResponse
{
    public ?ShortcutUrl $shortcut_url = null;

    public ?CustomerUrl $customer_url = null;

    public ?string $shortcut = null;
}
