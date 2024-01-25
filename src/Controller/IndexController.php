<?php declare(strict_types=1);
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractAppController
{
    #[Route(
        path:'/',
        name: 'index',
        methods: [
            Request::METHOD_GET,
            Request::METHOD_POST
        ]
    )]
    function index()
    {

    }
}
