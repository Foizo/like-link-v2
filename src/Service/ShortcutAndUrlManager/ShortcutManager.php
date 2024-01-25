<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Models\ShortcutAndUrl\ShortcutRequest;
use App\Models\ShortcutAndUrl\ShortcutResponse;

class ShortcutManager extends AbstractShortcutAndUrlManager
{
    function getUrl(ShortcutRequest $shortcut_request): ?ShortcutResponse
    {
        $exist_shortcut_url = $this->existShortcut($shortcut_request);

        if (!$exist_shortcut_url) {
            return null;
        }

        $shortcut_response = new ShortcutResponse();
        $shortcut_response->shortcut_url = $exist_shortcut_url;
        $shortcut_response->shortcut = $shortcut_request->shortcut;
        $shortcut_response->destination_url = $exist_shortcut_url->customer_url->destination_url;
        $shortcut_response->customer_url = $exist_shortcut_url->customer_url;

        return $shortcut_response;
    }
}
