<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Entity\ShortcutUrl;
use App\Doctrine\Repository\CustomerUrlsRepository;
use App\Doctrine\Repository\ShortcutsUrlsRepository;
use App\Models\ShortcutAndUrl\ShortcutRequest;
use App\Models\ShortcutAndUrl\ShortUrlRequest;

abstract class AbstractShortcutAndUrlManager
{
    function __construct(
        protected ShortcutsUrlsRepository $shortcut_repo,
        protected CustomerUrlsRepository $customer_repo
    ){}

    protected function existUrl(ShortUrlRequest $short_url_request): ?CustomerUrl
    {
        return $this->customer_repo->findOneByDestinationUrlHash($short_url_request->app_domain, $short_url_request->destination_url_md5_hash);
    }

    protected function existShortcut(ShortcutRequest $shortcut_response): ?ShortcutUrl
    {
        return $this->shortcut_repo->findOneByGeneratedOrCustomerShortcut($shortcut_response->app_domain, $shortcut_response->shortcut);
    }
}