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


    function getShortCut(ShortUrlRequest $short_url_request): ShortUrlRequest
    {
        $shortcut_iteration = 0;
        do {
            $shortcut_iteration++;
            if ($shortcut_iteration > GeneratedShortcut::SHORTCUT_MAX_GENERATE_ITERATION) {
                $this->logger->error(__CLASS__ . ": exceeded max count of iteration");
                $short_url_request->valid_request = false;
                return $short_url_request;
            }

            $short_url_request->shortcut = $this->generateShortcut();

        } while ($this->isExistUniqueShortcut($short_url_request));

        $this->logger->info(__CLASS__ . ": generated shortcut is unique", ['shortcut' => $short_url_request->shortcut]);
        return $short_url_request;
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
