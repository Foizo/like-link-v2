<?php declare(strict_types=1);
namespace App\Service;

use App\Doctrine\Entity\AppDomain;
use App\Models\SEO\SeoContent;
use Symfony\Component\HttpFoundation\RequestStack;

class SeoContentProvider
{
    protected SeoContent $seo_content;


    function __construct(
        protected AppDomain $current_app,
        protected RequestStack $request_stack
    ){}


    function getSeoContent(): SeoContent
    {
        if (!isset($this->seo_content)) {
            $this->initSeoContent();
        }

        return $this->seo_content;
    }

    protected function initSeoContent(): void
    {
        $this->seo_content = new SeoContent();
        $this->seo_content->og_url = $this->request_stack->getCurrentRequest()?->getUri()?? sprintf("https://%s", $this->current_app->domain);
        $this->seo_content->base_url = sprintf("https://%s", $this->current_app->domain);
    }
}
