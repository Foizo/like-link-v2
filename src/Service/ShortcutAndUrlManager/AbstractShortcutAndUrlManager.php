<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Repository\CustomerUrlsRepository;
use App\Models\ShortcutAndUrl\ShortUrlRequest;
use Psr\Log\LoggerInterface;

abstract class AbstractShortcutAndUrlManager
{
    function __construct(
        protected AppDomain $current_app,
        protected CustomerUrlsRepository $customer_repo,
        protected LoggerInterface $logger
    ){}

    protected function existUrl(ShortUrlRequest $short_url_request): ?CustomerUrl
    {
        return $this->customer_repo->findOneByDestinationUrlHash($this->current_app, $short_url_request->destination_url_md5_hash);
    }

    protected function existShortcut(string $shortcut): ?CustomerUrl
    {
        return $this->customer_repo->findOneByGeneratedOrCustomerShortcut($this->current_app, $shortcut);
    }

    protected function resolveRedirectLink(ShortUrlRequest $short_url_request): string {
        return  sprintf("https://%s/%s", $this->current_app->domain, $short_url_request->shortcut);
    }
}