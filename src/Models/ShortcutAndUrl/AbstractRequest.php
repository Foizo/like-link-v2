<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

use App\Doctrine\Entity\AppDomain;

abstract class AbstractRequest
{
    public AppDomain $app_domain;

    public ?string $shortcut = null;
}
