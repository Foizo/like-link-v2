<?php declare(strict_types=1);
namespace App\Enums;

use App\Enums\Shortcut\CustomerShortcut;
use App\Enums\Shortcut\GeneratedShortcut;

class ShortUrl
{
    const URL_PATTERN = '/^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([\/\w.-]*)*\/?$/';

    const BLOCKED_CUSTOMER_SHORTCUTS = [
        'likelink',
        'like-link',
        'your-like-link',
        CustomerShortcut::NOT_SPECIFIED,
        GeneratedShortcut::NOT_GENERATED,
    ];

    const BLOCKED_DOMAINS_PATTERN = '~likelink~';
}