<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Models\ShortcutAndUrl\ShortcutResponse;

class ShortcutManager extends AbstractShortcutAndUrlManager
{
    function getUrl(string $shortcut): ?ShortcutResponse
    {
        $shortcut_response = new ShortcutResponse();

        $exist_shortcut_url = $this->existShortcut($shortcut);

        if (!$exist_shortcut_url) {
            return $shortcut_response;
        }

        $shortcut_response->customer_url = $exist_shortcut_url->customer_url;
        $shortcut_response->destination_url = $exist_shortcut_url->customer_url->destination_url;

        return $shortcut_response;
    }
}
