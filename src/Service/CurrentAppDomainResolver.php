<?php declare(strict_types=1);
namespace App\Service;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Repository\AppDomainsRepository;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CurrentAppDomainResolver
{
    protected AppDomain $current_app_domain;

    function __construct(
        #[Autowire("%env(CURRENT_APP_IDENTIFIER)%")]
        private readonly string $current_app_identifier,
        private readonly AppDomainsRepository $app_repo
    ){}

    function resolveCurrentApp(): AppDomain
    {
        if(isset($this->current_app_domain)){
            return $this->current_app_domain;
        }

        $app = $this->app_repo->findByIdentifier($this->current_app_identifier, true);
        if(!$app instanceof AppDomain){
            throw new RuntimeException("Cannot resolve current app domain - app domain with identifier '{$this->current_app_identifier}' does not exist");
        }
        $this->current_app_domain = $app;
        return $app;
    }
}