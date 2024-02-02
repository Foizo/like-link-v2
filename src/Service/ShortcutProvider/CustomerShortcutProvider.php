<?php declare(strict_types=1);
namespace App\Service\ShortcutProvider;

use App\Enums\Shortcut\CustomerShortcut;
use App\Models\ShortcutAndUrl\ShortUrlRequest;

class CustomerShortcutProvider extends AbstractShortcutProvider
{
    function shortcutPattern(): string
    {
        return CustomerShortcut::SHORTCUT_PATTERN;
    }


    function getShortCut(ShortUrlRequest $short_url_request): ShortUrlRequest
    {
        if (!$short_url_request->shortcut) {
            $short_url_request->valid_request= false;
            $this->logger->error(__CLASS__ . ": given customer shortcut is null");
        }

        if (!preg_match($this->shortcutPattern(), $short_url_request->shortcut)) {
            $short_url_request->valid_request = false;
            $this->logger->error(__CLASS__ . ": customer shortcut does not match pattern conditions", ['shortcut' => $short_url_request->shortcut]);
        }

        if ($this->isExistUniqueShortcut($short_url_request)) {
            $short_url_request->valid_request = false;
            $this->logger->error(__CLASS__ . ": customer shortcut does not unique", ['shortcut' => $short_url_request->shortcut]);
        }

        if ($short_url_request->valid_request) {
            $this->logger->info(__CLASS__ . ": customer shortcut is unique", ['shortcut' => $short_url_request->shortcut]);
        }
        return $short_url_request;
    }
}
