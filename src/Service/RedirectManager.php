<?php declare(strict_types=1);
namespace App\Service;

use App\Doctrine\Entity\AppDomain;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Entity\CustomerUrl\CustomerUrlRedirect;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class RedirectManager
{
    function __construct(
        protected AppDomain $current_app,
        protected EntityManagerInterface $em
    ){}


    function createRecordOfRedirect(Request $request, CustomerUrl $customer_url): void
    {
        $record_of_redirect = new CustomerUrlRedirect();
        $record_of_redirect->customer_url = $customer_url;
        $record_of_redirect->app_domain = $this->current_app;
        $record_of_redirect->params->fillFromRequest($request);

        $this->em->persist($record_of_redirect);
        $this->em->flush();
    }
}
