<?php declare(strict_types=1);
namespace App\Service\Notifications\EmailNotifications;

use App\Doctrine\Entity\AppDomain;
use App\Service\Notifications\EmailDeliveryService;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Throwable;

abstract class AbstractEmailNotifier
{
    function __construct(
        private EmailDeliveryService $email_delivery_service,
        protected AppDomain $current_app,
        protected LoggerInterface $logger
    ){}


    abstract function getTemplateIdentifier(): string;


    protected function createEmail(Address $address, string $subject, array $context_parameters = []): Email
    {
        return (new TemplatedEmail())
            ->to($address)
            ->subject($subject)
            ->htmlTemplate($this->getEmailTemplate())
            ->context($context_parameters);
    }

    protected function sendEmail(Address $address, string $subject, array $context_parameters = []): bool
    {
        $this->logger->info(__CLASS__ . ": Creating email type '{$this->getTemplateIdentifier()}' with subject '{$subject}'");

        $email = $this->createEmail($address, $subject, $context_parameters);

        try {
            $this->email_delivery_service->sendEmail($email, $this->getTemplateIdentifier());
            return true;
        } catch (Throwable $e) {
            $this->logger->error(__CLASS__ . ": Email type '{$this->getTemplateIdentifier()}' with subject '{$subject}' send fail. Error: {$e->getMessage()}");
            return false;
        }
    }


    private function getEmailTemplate(): string
    {
        return sprintf(
            "@email/%s-%s.html.twig",
            $this->getTemplateIdentifier(),
            $this->current_app->language->value
        );
    }

}