<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

class ShortUrlResponse extends AbstractResponse
{
    public ?string $redirect_link = null;
}