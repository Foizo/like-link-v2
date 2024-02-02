<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Models\ShortcutAndUrl\ShortcutRequest;
use App\Models\ShortcutAndUrl\ShortcutResponse;
use Exception;

class ShortcutManager extends AbstractShortcutAndUrlManager
{
    /** @throws Exception */
    function getUrl(ShortcutRequest $shortcut_request): ?ShortcutResponse
    {
        $exist_shortcut_url = $this->existShortcut($shortcut_request);

        if (!$exist_shortcut_url) {
            throw new Exception("For given ShortcutRequest is no record! [shortcut: {$shortcut_request->shortcut}]");
        }

        $shortcut_response = new ShortcutResponse();
        $shortcut_response->destination_url = $exist_shortcut_url->customer_url->destination_url;

        return $shortcut_response;
    }
}
