<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Entity\ShortcutUrl;
use App\Doctrine\Repository\CustomerUrlsRepository;
use App\Doctrine\Repository\ShortcutsUrlsRepository;
use App\Models\ShortcutAndUrl\ShortUrlRequest;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractShortcutAndUrlManager
{
    function __construct(
        protected AppDomain $current_app,
        protected ShortcutsUrlsRepository $shortcut_repo,
        protected CustomerUrlsRepository $customer_repo
    ){}

    protected function existUrl(ShortUrlRequest $short_url_request): ?CustomerUrl
    {
        $file = new Filesystem();
        $file->appendToFile('../../../var/exist.txt', 'ahoj');
        return $this->customer_repo->findOneByDestinationUrlHash($this->current_app, $short_url_request->destination_url_md5_hash);
    }

    protected function existShortcut(string $shortcut): ?ShortcutUrl
    {
        return $this->shortcut_repo->findOneByGeneratedOrCustomerShortcut($this->current_app, $shortcut);
    }

    protected function resolveRedirectLink(ShortUrlRequest $short_url_request): string {
        return  sprintf("https://%s/%s", $this->current_app->domain, $short_url_request->shortcut);
    }
}