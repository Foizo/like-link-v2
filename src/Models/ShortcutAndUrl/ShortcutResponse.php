<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

class ShortcutResponse extends AbstractResponse
{
    public ?string $destination_url = null;
}