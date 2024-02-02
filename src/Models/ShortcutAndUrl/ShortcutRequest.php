<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

use Symfony\Component\HttpFoundation\Request;

class ShortcutRequest
{
    public Request $request;

    public string $shortcut;
}
