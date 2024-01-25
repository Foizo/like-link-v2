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


    function getShortCut(ShortUrlRequest $app_shortcut_request): ShortUrlRequest
    {
        if (!$app_shortcut_request->shortcut) {
            $app_shortcut_request->valid_request= false;
            $this->logger->error(__CLASS__ . ": given customer shortcut is null");
        }

        if (!preg_match($this->shortcutPattern(), $app_shortcut_request->shortcut)) {
            $app_shortcut_request->valid_request = false;
            $this->logger->error(__CLASS__ . ": customer shortcut does not match pattern conditions", ['shortcut' => $app_shortcut_request->shortcut]);
        }

        if (!$this->isUniqueShortcut($app_shortcut_request)) {
            $app_shortcut_request->valid_request = false;
            $this->logger->error(__CLASS__ . ": customer shortcut does not unique", ['shortcut' => $app_shortcut_request->shortcut]);
        }

        if ($app_shortcut_request->valid_request) {
            $this->logger->info(__CLASS__ . ": customer shortcut is unique", ['shortcut' => $app_shortcut_request->shortcut]);
        }
        return $app_shortcut_request;
    }
}
