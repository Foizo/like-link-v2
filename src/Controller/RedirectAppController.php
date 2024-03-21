<?php declare(strict_types=1);
namespace App\Controller;

use App\Service\RedirectManager;
use App\Service\ShortcutAndUrlManager\ShortcutManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class RedirectAppController extends AbstractController
{
    function __construct(
        protected ShortcutManager $shortcut_manager,
        protected RedirectManager $redirect_generator
    ){}


    #[Route(
        path: '/{shortcut}',
        name: 'redirect',
        requirements: ['shortcut' => Requirement::ASCII_SLUG],
        methods: Request::METHOD_GET
    )]
    function redirectByShortcut(Request $request, string $shortcut): Response
    {
        $shortcut_response = $this->shortcut_manager->getUrl($shortcut);

        if (!$shortcut_response->customer_url) {
            throw $this->createNotFoundException('404');
        }

        $this->redirect_generator->createRecordOfRedirect($request, $shortcut_response->customer_url);

        return $this->render(
            view: 'redirect.html.twig',
            parameters: [
                'redirect_url' => $shortcut_response->destination_url
            ]
        );
    }
}
