<?php declare(strict_types=1);
namespace App\Controller;

use App\Form\Handler\ShortUrlFormHandler;
use App\Form\ShortUrlForm;
use App\Service\SeoContentProvider;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class IndexAppController extends AbstractController
{
    function __construct(
        protected ShortUrlFormHandler $short_url_form_handler,
    ){}


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

            try {
                $short_url_response = $this->short_url_form_handler->handleForm($form);
            } catch (Throwable $e) {
                throw new RuntimeException("ShortUrl Form Handler error: {$e->getMessage()}");
            }

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
