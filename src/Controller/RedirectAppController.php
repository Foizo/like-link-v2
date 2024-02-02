<?php declare(strict_types=1);
namespace App\Controller;

use App\Doctrine\Entity\AppDomain;
use App\Service\ShortcutAndUrlManager\ShortcutManager;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class RedirectAppController extends AbstractAppController
{
    #[Pure]
    function __construct(
        AppDomain $app_domain,
        RequestStack $request_stack,
        protected ShortcutManager $shortcut_manager
    )
    {
        parent::__construct($app_domain, $request_stack);
    }

    #[Route(
        path: '/{shortcut}',
        name: 'redirect',
        requirements: ['shortcut' => Requirement::ASCII_SLUG],
        methods: Request::METHOD_GET
    )]
    function redirectByShortcut(string $shortcut)
    {
        return new Response();
    }
}
