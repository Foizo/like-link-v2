<?php declare(strict_types=1);
namespace App\EventListener;

use App\Doctrine\Entity\AppDomain;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    function __construct(
        protected AppDomain $current_app
    ){}


    function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $request->setLocale($this->current_app->language->value);
    }

    static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}