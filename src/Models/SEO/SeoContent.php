<?php declare(strict_types=1);
namespace App\Models\SEO;

use App\Doctrine\Entity\AppDomain;

class SeoContent
{
    public string $og_title = '';

    public string $og_type = '';

    public string $og_url = '';

    public string $og_description = '';

    public string $description = '';

    public string $keywords = '';

    public string $title = '';

    public string $base_url = '';
}