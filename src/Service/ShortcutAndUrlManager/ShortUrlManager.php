<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Entity\ShortcutUrl;
use App\Doctrine\Repository\CustomerUrlsRepository;
use App\Doctrine\Repository\ShortcutsUrlsRepository;
use App\Enums\Shortcut\CustomerShortcut;
use App\Enums\Shortcut\GeneratedShortcut;
use App\Form\ShortUrlForm;
use App\Models\ShortcutAndUrl\ShortUrlRequest;
use App\Models\ShortcutAndUrl\ShortUrlResponse;
use App\Service\ShortcutProvider\CustomerShortcutProvider;
use App\Service\ShortcutProvider\GeneratedShortcutProvider;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class ShortUrlManager extends AbstractShortcutAndUrlManager
{
    function __construct(
        protected AppDomain $current_app,
        protected CustomerShortcutProvider $customer_shortcut_provider,
        protected GeneratedShortcutProvider $generated_shortcut_provider,
        protected EntityManagerInterface $em,
        protected ShortcutsUrlsRepository $shortcut_repo,
        protected CustomerUrlsRepository $customer_repo,
        protected TranslatorInterface $translator
    )
    {
        parent::__construct($this->current_app, $this->shortcut_repo, $this->customer_repo);
    }


    function shortUrl(ShortUrlRequest $short_url_request): ShortUrlResponse
    {
        $exist_url = $this->existUrl($short_url_request);

        if ($exist_url) {
            return $this->resolveExistShortUrlResponse($exist_url, $short_url_request);
        }

        if ($short_url_request->customer_shortcut) {
            $short_url_request = $this->customer_shortcut_provider->getShortcut($short_url_request);
        } else {
            $short_url_request = $this->generated_shortcut_provider->getShortcut($short_url_request);
        }

        $short_url_response = new ShortUrlResponse();

        if (!$short_url_request->valid_request) {
            $short_url_response->valid_response = false;
            $short_url_response->errors = [ShortUrlForm::SUBMIT_CHILD_NAME => $this->translator->trans('app.unexpected_error', domain: 'errors')];
            return $short_url_response;
        }

        try {
            $shortcut_url = $this->createShortcut($short_url_request);
        } catch (Throwable $e) {
            $short_url_response->valid_response = false;
            $short_url_response->errors = [ShortUrlForm::SUBMIT_CHILD_NAME => $this->translator->trans('app.unexpected_error', domain: 'errors')];
            return $short_url_response;
        }

        $short_url_response->shortcut = $short_url_request->customer_shortcut ? $shortcut_url->customer_shortcut : $shortcut_url->generated_shortcut;
        $short_url_response->redirect_link = $this->resolveRedirectLink($short_url_request);

        return  $short_url_response;
    }


    private function resolveExistShortUrlResponse(CustomerUrl $customer_url, ShortUrlRequest $short_url_request): ShortUrlResponse
    {
        $short_url_response = new ShortUrlResponse();

        if ($short_url_request->customer_shortcut) {

            if ($customer_url->shortcut_url->customer_shortcut === CustomerShortcut::NOT_SPECIFIED) {

                try {
                    $updated_shortcut_url = $this->createCustomerShortcutForExistUrl($short_url_request, $customer_url->shortcut_url);
                } catch (Throwable $e) {
                    $short_url_response->valid_response = false;
                    $short_url_response->errors = [ShortUrlForm::DESTINATION_URL_CHILD_NAME=> $this->translator->trans('app.alias_error', domain: 'errors')];
                    return $short_url_response;
                }

                $short_url_response->shortcut = $updated_shortcut_url->customer_shortcut;
            } else {

                if ($customer_url->shortcut_url->customer_shortcut !== $short_url_request->shortcut) {
                    $short_url_response->valid_response = false;
                    $short_url_response->errors = [ShortUrlForm::SHORTCUT_CHILD_NAME => $this->translator->trans('app.no_alias_error', domain: 'errors')];
                }

                $short_url_response->shortcut = $customer_url->shortcut_url->customer_shortcut;
            }

        } else {

            if ($customer_url->shortcut_url->generated_shortcut === GeneratedShortcut::NOT_GENERATED) {

                try {
                    $updated_shortcut_url = $this->createGeneratedShortcutForExistUrl($short_url_request, $customer_url->shortcut_url);
                } catch (Throwable $e) {
                    $short_url_response->valid_response = false;
                    $short_url_response->errors = [ShortUrlForm::SUBMIT_CHILD_NAME => $this->translator->trans('app.unexpected_error', domain: 'errors')];
                    return $short_url_response;
                }

                $short_url_response->shortcut = $updated_shortcut_url->generated_shortcut;
            } else {
                $short_url_response->shortcut = $customer_url->shortcut_url->generated_shortcut;
            }

        }

        $short_url_request->shortcut = $short_url_response->shortcut;
        $short_url_response->redirect_link = $this->resolveRedirectLink($short_url_request);
        return $short_url_response;
    }

    /** @throws Exception */
    private function createShortcut(ShortUrlRequest $short_url_request): ShortcutUrl
    {
        try {
            $this->em->beginTransaction();

            $customer_url = new CustomerUrl();
            $customer_url->app_domain = $this->current_app;
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
            $shortcut_url->app_domain = $this->current_app;

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

    /** @throws Exception */
    private function createCustomerShortcutForExistUrl(ShortUrlRequest $short_url_request, ShortcutUrl $shortcut_url): ShortcutUrl
    {
        $short_url_request = $this->customer_shortcut_provider->getShortcut($short_url_request);

        if (!$short_url_request->valid_request) {
            throw new Exception("Given ShortUrlRequest for customer shortcut on exist url is not valid! [shortcut: {$short_url_request->shortcut}]");
        }

        try {
            $this->em->beginTransaction();

            $shortcut_url->customer_shortcut = $short_url_request->shortcut;
            $this->em->persist($shortcut_url);

            $this->em->flush();
            $this->em->commit();
            $this->em->clear();
        } catch (Throwable $e) {
            throw new Exception("Create customer shortcut on exist url fail! Error: {$e->getMessage()}");
        }

        return $shortcut_url;
    }
}