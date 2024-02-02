<?php declare(strict_types=1);
namespace App\Controller;

use App\Doctrine\Entity\AppDomain;
use App\Form\Handler\ShortUrlFormHandler;
use App\Form\ShortUrlForm;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexAppController extends AbstractAppController
{
    #[Pure]
    function __construct(
        AppDomain $app_domain,
        RequestStack $request_stack,
        protected ShortUrlFormHandler $short_url_form_handler
    )
    {
        parent::__construct($app_domain, $request_stack);
    }

    #[Route(
        path:'/',
        name: 'index',
        methods: [
            Request::METHOD_GET,
            Request::METHOD_POST
        ]
    )]
    function index(Request $request): Response
    {
        $form = $this->createForm(ShortUrlForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $short_url_response = $this->short_url_form_handler->handleForm($form);
            return new JsonResponse($short_url_response);
        }

        return $this->render(
            view: 'index.html.twig',
            parameters: [
                'short_url_form' => $form->createView()
            ]
        );
    }
}
