<?php declare(strict_types=1);
namespace App\Service;

use App\Doctrine\Entity\AppDomain;
use App\Models\SEO\SeoContent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class SeoContentProvider
{
    protected SeoContent $seo_content;


    function __construct(
        protected AppDomain $current_app,
        protected RequestStack $request_stack,
        protected TranslatorInterface $translator
    ){}


    function getSeoContent(): SeoContent
    {
        if (!isset($this->seo_content)) {
            $this->initSeoContent();
        }

        return $this->seo_content;
    }

    protected function initSeoContent()
    {
        $this->seo_content = new SeoContent();

        $this->seo_content->title = $this->translator->trans('layout.title');
        $this->seo_content->description = $this->translator->trans('layout.description');
        $this->seo_content->keywords = $this->translator->trans('layout.keywords');

        $this->seo_content->og_title = $this->translator->trans('layout.og.title');
        $this->seo_content->og_type = $this->translator->trans('layout.og.type');
        $this->seo_content->og_description = $this->translator->trans('layout.og.description');
        $this->seo_content->og_url = $this->request_stack->getCurrentRequest()?->getUri()?? sprintf("https://%s", $this->current_app->domain);

        $this->seo_content->base_url = sprintf("https://%s", $this->current_app->domain);
    }
}
