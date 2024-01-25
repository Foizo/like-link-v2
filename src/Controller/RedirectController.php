<?php declare(strict_types=1);
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class RedirectController extends AbstractAppController
{
    #[Route(
        path: '/{shortcut}',
        name: 'redirect',
        requirements: ['shortcut' => Requirement::ASCII_SLUG],
        methods: Request::METHOD_GET
    )]
    function redirectByShortcut(string $shortcut)
    {

    }
}
