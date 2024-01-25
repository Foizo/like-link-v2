<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Entity\ShortcutUrl;
use App\Doctrine\Repository\CustomerUrlsRepository;
use App\Doctrine\Repository\ShortcutsUrlsRepository;
use App\Enums\Shortcut\GeneratedShortcut;
use App\Models\ShortcutAndUrl\ShortUrlRequest;
use App\Models\ShortcutAndUrl\ShortUrlResponse;
use App\Service\ShortcutProvider\CustomerShortcutProvider;
use App\Service\ShortcutProvider\GeneratedShortcutProvider;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Throwable;

class ShortUrlManager extends AbstractShortcutAndUrlManager
{
    function __construct(
        private readonly CustomerShortcutProvider $customer_shortcut_provider,
        private readonly GeneratedShortcutProvider $generated_shortcut_provider,
        protected EntityManagerInterface $em,
        protected ShortcutsUrlsRepository $shortcut_repo,
        protected CustomerUrlsRepository $customer_repo
    )
    {
        parent::__construct($this->shortcut_repo, $this->customer_repo);
    }

    /** @throws Exception */
    function shortUrl(ShortUrlRequest $short_url_request): ShortUrlResponse
    {
        $exist_url = $this->existUrl($short_url_request);

        $short_url_response = new ShortUrlResponse();

        if ($exist_url) {
            $short_url_response->customer_url = $exist_url;

            if ($exist_url->shortcut_url->generated_shortcut === GeneratedShortcut::NOT_GENERATED) {
                $updated_shortcut_url = $this->createGeneratedShortcutForExistUrl($short_url_request, $exist_url->shortcut_url);
                $short_url_response->shortcut = $updated_shortcut_url->generated_shortcut;
            } else {
                $short_url_response->shortcut = $exist_url->shortcut_url->generated_shortcut;
            }

            return $short_url_response;
        }

        if ($short_url_request->customer_shortcut) {
            $short_url_request = $this->customer_shortcut_provider->getShortcut($short_url_request);
        } else {
            $short_url_request = $this->generated_shortcut_provider->getShortcut($short_url_request);
        }

        if (!$short_url_request->valid_request) {
            throw new Exception(
                "Given ShortUrlRequest is not valid for create shortcut!"
                . $short_url_request->shortcut
                    ? "[shortcut: {$short_url_request->shortcut}]"
                    : ''
            );
        }

        $shortcut_url = $this->createShortcut($short_url_request);

        $short_url_response->customer_url = $shortcut_url->customer_url;
        $short_url_response->shortcut = $short_url_request->customer_shortcut ? $shortcut_url->customer_shortcut : $shortcut_url->generated_shortcut;

        return  $short_url_response;
    }


    /** @throws Exception */
    private function createShortcut(ShortUrlRequest $short_url_request): ShortcutUrl
    {
        try {
            $this->em->beginTransaction();

            $customer_url = new CustomerUrl();
            $customer_url->app_domain = $short_url_request->app_domain;
            $customer_url->destination_url = $short_url_request->destination_url;
            $customer_url->destination_url_md5_hash = $short_url_request->destination_url_md5_hash;

            $customer_url->params->scheme = parse_url($short_url_request->destination_url, PHP_URL_SCHEME);
            $customer_url->params->host = parse_url($short_url_request->destination_url, PHP_URL_HOST);
            $customer_url->params->port = parse_url($short_url_request->destination_url, PHP_URL_PORT);
            $customer_url->params->user = parse_url($short_url_request->destination_url, PHP_URL_USER);
            $customer_url->params->pass = parse_url($short_url_request->destination_url, PHP_URL_PASS);
            $customer_url->params->path = parse_url($short_url_request->destination_url, PHP_URL_PATH);
            $customer_url->params->fragment = parse_url($short_url_request->destination_url, PHP_URL_FRAGMENT);

            $query = parse_url($short_url_request->destination_url, PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $customer_url->params->queries);
            }

            $shortcut_url = new ShortcutUrl();
            $shortcut_url->app_domain = $short_url_request->app_domain;

            if ($short_url_request->customer_shortcut) {
                $shortcut_url->customer_shortcut = $short_url_request->shortcut;
            } else {
                $shortcut_url->generated_shortcut = $short_url_request->shortcut;
            }

            $customer_url->shortcut_url = $shortcut_url;
            $shortcut_url->customer_url = $customer_url;

            $this->em->persist($customer_url);
            $this->em->persist($shortcut_url);

            $this->em->flush();

            $this->em->commit();
            $this->em->clear();
        } catch (Throwable $e) {
            throw new Exception("Create ShortcutUrl fail! Error: {$e->getMessage()}");
        }

        return $shortcut_url;
    }

    /** @throws Exception */
    private function createGeneratedShortcutForExistUrl(ShortUrlRequest $short_url_request, ShortcutUrl $shortcut_url): ShortcutUrl
    {
        $short_url_request = $this->generated_shortcut_provider->getShortcut($short_url_request);

        if (!$short_url_request->valid_request) {
            throw new Exception("Given ShortUrlRequest for generate shortcut on exist url is not valid! [shortcut: {$short_url_request->shortcut}]");
        }

        try {
            $this->em->beginTransaction();

            $shortcut_url->generated_shortcut = $short_url_request->shortcut;
            $this->em->persist($shortcut_url);

            $this->em->flush();
            $this->em->commit();
            $this->em->clear();
        } catch (Throwable $e) {
            throw new Exception("Create generated shortcut on exist url fail! Error: {$e->getMessage()}");
        }

        return $shortcut_url;
    }
}