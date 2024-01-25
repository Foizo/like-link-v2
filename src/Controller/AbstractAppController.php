<?php declare(strict_types=1);
namespace App\Controller;

use App\Doctrine\Entity\AppDomain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class AbstractAppController extends AbstractController
{
    function __construct(
        protected AppDomain $current_app,
        private readonly RequestStack $request_stack
    ){}

    protected function getSession(): SessionInterface
    {
        return $this->request_stack->getSession();
    }
}