<?php declare(strict_types=1);
namespace App\Doctrine\Entity\CustomerUrl\Parts;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;

#[ORM\Embeddable]
class CustomerUrlRedirectParameters
{
    #[ORM\Column(length: 32, nullable: true)]
    public ?string $ip = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $user_agent = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $referer = null;

    #[ORM\Column(type: Types::TEXT,nullable: true)]
    public ?string $url = null;


    function fillFromRequest(Request $request)
    {
        $this->ip = (string)$request->getClientIp();
        $this->user_agent = (string)$request->headers->get('User-Agent');
        $this->referer = (string)$request->server->get('HTTP_REFERER');
        $this->url = $request->getUri();
    }
}
