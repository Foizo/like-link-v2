<?php declare(strict_types=1);
namespace App\Service\ShortcutAndUrlManager;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Repository\CustomerUrlsRepository;
use App\Form\ShortUrlForm;
use App\Models\ShortcutAndUrl\ShortUrlRequest;
use App\Models\ShortcutAndUrl\ShortUrlResponse;
use App\Service\ShortcutProvider\CustomerShortcutProvider;
use App\Service\ShortcutProvider\GeneratedShortcutProvider;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class ShortUrlManager extends AbstractShortcutAndUrlManager
{
    function __construct(
        AppDomain $current_app,
        CustomerUrlsRepository $customer_repo,
        LoggerInterface $logger,
        protected CustomerShortcutProvider $customer_shortcut_provider,
        protected GeneratedShortcutProvider $generated_shortcut_provider,
        protected EntityManagerInterface $em,
        protected TranslatorInterface $translator
    )
    {
        parent::__construct($current_app, $customer_repo, $logger);
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
            $customer_url = $this->createCustomerUrl($short_url_request);
            $this->logger->info(__CLASS__ . ": #{$customer_url->id} CustomerUrl is created");
        } catch (Throwable $e) {
            $this->logger->error(__CLASS__ . ": Create CustomerUrl fail: {$e->getMessage()}");
            $short_url_response->valid_response = false;
            $short_url_response->errors = [ShortUrlForm::SUBMIT_CHILD_NAME => $this->translator->trans('app.unexpected_error', domain: 'errors')];
            return $short_url_response;
        }

        $short_url_response->shortcut = $short_url_request->customer_shortcut ?
            $customer_url->shortcuts->customer_shortcut :
            $customer_url->shortcuts->generated_shortcut;
        $short_url_response->redirect_link = $this->resolveRedirectLink($short_url_request);

        return  $short_url_response;
    }


    private function resolveExistShortUrlResponse(CustomerUrl $customer_url, ShortUrlRequest $short_url_request): ShortUrlResponse
    {
        $short_url_response = new ShortUrlResponse();

        if ($short_url_request->customer_shortcut) {

            if (!$customer_url->shortcuts->customer_shortcut) {

                try {
                    $updated_customer_url = $this->createCustomerShortcutForExistUrl($short_url_request, $customer_url);
                    $this->logger->info(__CLASS__ . ": #{$updated_customer_url->id} CustomerUrl (customer shortcut) is updated.");
                } catch (Throwable $e) {
                    $this->logger->error(__CLASS__ . ": Updating CustomerUrl (customer shortcut) fail: {$e->getMessage()}");
                    $short_url_response->valid_response = false;
                    $short_url_response->errors = [ShortUrlForm::DESTINATION_URL_CHILD_NAME=> $this->translator->trans('app.alias_error', domain: 'errors')];
                    return $short_url_response;
                }

                $short_url_response->shortcut = $updated_customer_url->shortcuts->customer_shortcut;
            } else {

                if ($customer_url->shortcuts->customer_shortcut !== $short_url_request->shortcut) {
                    $short_url_response->valid_response = false;
                    $short_url_response->errors = [ShortUrlForm::SHORTCUT_CHILD_NAME => $this->translator->trans('app.no_alias_error', domain: 'errors')];
                }

                $short_url_response->shortcut = $customer_url->shortcuts->customer_shortcut;
            }

        } else {

            if (!$customer_url->shortcuts->generated_shortcut) {

                try {
                    $updated_customer_url = $this->createGeneratedShortcutForExistUrl($short_url_request, $customer_url);
                    $this->logger->info(__CLASS__ . ": #{$updated_customer_url->id} CustomerUrl (generated shortcut) is updated");
                } catch (Throwable $e) {
                    $this->logger->error(__CLASS__ . ": Updating CustomerUrl (generated shortcut) fail: {$e->getMessage()}");
                    $short_url_response->valid_response = false;
                    $short_url_response->errors = [ShortUrlForm::SUBMIT_CHILD_NAME => $this->translator->trans('app.unexpected_error', domain: 'errors')];
                    return $short_url_response;
                }

                $short_url_response->shortcut = $updated_customer_url->shortcuts->generated_shortcut;
            } else {
                $short_url_response->shortcut = $customer_url->shortcuts->generated_shortcut;
            }

        }

        $short_url_request->shortcut = $short_url_response->shortcut;
        $short_url_response->redirect_link = $this->resolveRedirectLink($short_url_request);
        return $short_url_response;
    }

    /** @throws Exception */
    private function createCustomerUrl(ShortUrlRequest $short_url_request): CustomerUrl
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

            if ($short_url_request->customer_shortcut) {
                $customer_url->shortcuts->customer_shortcut = $short_url_request->shortcut;
            } else {
                $customer_url->shortcuts->generated_shortcut = $short_url_request->shortcut;
            }

            $this->em->persist($customer_url);

            $this->em->flush();
            $this->em->commit();
        } catch (Throwable $e) {
            $this->em->rollback();
            throw new Exception("Create CustomerUrl fail: {$e->getMessage()}");
        }

        return $customer_url;
    }

    /** @throws Exception */
    private function createGeneratedShortcutForExistUrl(ShortUrlRequest $short_url_request, CustomerUrl $customer_url): CustomerUrl
    {
        $short_url_request = $this->generated_shortcut_provider->getShortcut($short_url_request);

        if (!$short_url_request->valid_request) {
            throw new Exception("Given ShortUrlRequest for generate shortcut on exist url is not valid! [shortcut: {$short_url_request->shortcut}]");
        }

        try {
            $this->em->beginTransaction();

            $customer_url->shortcuts->generated_shortcut = $short_url_request->shortcut;
            $this->em->persist($customer_url);

            $this->em->flush();
            $this->em->commit();
        } catch (Throwable $e) {
            throw new Exception("Create generated shortcut on exist url fail! Error: {$e->getMessage()}");
        }

        return $customer_url;
    }

    /** @throws Exception */
    private function createCustomerShortcutForExistUrl(ShortUrlRequest $short_url_request, CustomerUrl $customer_url): CustomerUrl
    {
        $short_url_request = $this->customer_shortcut_provider->getShortcut($short_url_request);

        if (!$short_url_request->valid_request) {
            throw new Exception("Given ShortUrlRequest for customer shortcut on exist url is not valid! [shortcut: {$short_url_request->shortcut}]");
        }

        try {
            $this->em->beginTransaction();

            $customer_url->shortcuts->customer_shortcut = $short_url_request->shortcut;
            $this->em->persist($customer_url);

            $this->em->flush();
            $this->em->commit();
        } catch (Throwable $e) {
            throw new Exception("Create customer shortcut on exist url fail! Error: {$e->getMessage()}");
        }

        return $customer_url;
    }
}