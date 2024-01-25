<?php declare(strict_types=1);
namespace App\Models\ShortcutAndUrl;

class ShortUrlRequest extends AbstractRequest
{
    public string $destination_url;

    public string $destination_url_md5_hash;

    public bool $customer_shortcut = false;

    public bool $valid_request = true;
}