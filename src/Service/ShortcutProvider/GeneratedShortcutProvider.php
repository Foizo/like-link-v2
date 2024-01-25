<?php declare(strict_types=1);
namespace App\Service\ShortcutProvider;

use App\Enums\Shortcut\GeneratedShortcut;
use App\Models\ShortcutAndUrl\ShortUrlRequest;

class GeneratedShortcutProvider extends AbstractShortcutProvider
{

    function shortcutPattern(): string
    {
        return GeneratedShortcut::SHORTCUT_PATTERN;
    }


    function getShortCut(ShortUrlRequest $app_shortcut_request): ShortUrlRequest
    {
        $shortcut_iteration = 0;
        do {
            if ($shortcut_iteration++ > GeneratedShortcut::SHORTCUT_MAX_GENERATE_ITERATION) {
                $this->logger->error(__CLASS__ . ": exceeded max count of iteration");
                $app_shortcut_request->valid_request = false;
                return $app_shortcut_request;
            }

            $app_shortcut_request->shortcut = $this->generateShortcut();

        } while ($this->isUniqueShortcut($app_shortcut_request));

        $this->logger->info(__CLASS__ . ": generated shortcut is unique", ['shortcut' => $app_shortcut_request->shortcut]);
        return $app_shortcut_request;
    }


    private function generateShortcut(): string
    {
        $bytes = random_bytes(10);

        $base64_string = base64_encode(
            uniqid(
                $bytes . microtime(true),
                true
            )
        );

        $random_shortcut = substr(
            $base64_string,
            rand(0, GeneratedShortcut::SHORTCUT_MAX_LENGTH),
            rand(GeneratedShortcut::SHORTCUT_MIN_LENGTH, GeneratedShortcut::SHORTCUT_MAX_LENGTH)
        );

        if (!preg_match($this->shortcutPattern(), $random_shortcut)) {
            $random_shortcut = $this->generateShortCut();
        }

        return $random_shortcut;
    }
}
