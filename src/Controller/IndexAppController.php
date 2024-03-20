<?php declare(strict_types=1);
namespace App\Controller;

use App\Form\ContactForm;
use App\Form\Handler\ContactFormHandler;
use App\Form\Handler\ShortUrlFormHandler;
use App\Form\ShortUrlForm;
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
        protected ContactFormHandler $contact_form_handler
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
        $short_url_form = $this->createForm(ShortUrlForm::class);
        $contact_form = $this->createForm(ContactForm::class);

        $short_url_form->handleRequest($request);
        $contact_form->handleRequest($request);

        if ($short_url_form->isSubmitted()) {

            try {
                $short_url_response = $this->short_url_form_handler->handleForm($short_url_form);
            } catch (Throwable $e) {
                throw new RuntimeException("ShortUrl Form Handler error: {$e->getMessage()}");
            }

            return new JsonResponse($short_url_response);
        }

        if ($contact_form->isSubmitted()) {

            return new JsonResponse($this->contact_form_handler->handleForm($contact_form));
        }

        return $this->render(
            view: 'index.html.twig',
            parameters: [
                'short_url_form' => $short_url_form,
                'contact_form' => $contact_form
            ]
        );
    }
}
