<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Models\ShortcutAndUrl\ShortcutResponse;

class ShortcutManager extends AbstractShortcutAndUrlManager
{
    function getUrl(string $shortcut): ?ShortcutResponse
    {
        $shortcut_response = new ShortcutResponse();

        $exist_customer_url = $this->existShortcut($shortcut);

        if (!$exist_customer_url) {
            $this->logger->error(__CLASS__ . ": Shortcut: '{$shortcut}' not found.");
            return $shortcut_response;
        }

        $this->logger->info(__CLASS__ . ": #{$exist_customer_url->id} CustomerUrl find by shortcut: '{$shortcut}'");

        $shortcut_response->customer_url = $exist_customer_url;
        $shortcut_response->destination_url = $exist_customer_url->destination_url;

        return $shortcut_response;
    }
}
