<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

class ShortUrlResponse
{
    public ?string $redirect_link = null;

    public ?string $shortcut = null;

    public array $errors = [];

    public bool $valid_response = true;
}