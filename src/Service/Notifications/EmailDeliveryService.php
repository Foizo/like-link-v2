<?php declare(strict_types=1);
namespace App\Service\Notifications;

use App\Doctrine\Entity\AppDomain;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailDeliveryService
{
    function __construct(
        #[Autowire(env: 'DEFAULT_EMAIL_SENDER')]
        private string          $default_sender,
        private AppDomain       $current_app,
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ){}


    /** @throws TransportExceptionInterface */
    function sendEmail(Email $email, string $email_type): void
    {
        $email->from(new Address($this->default_sender, ucfirst($this->current_app->domain)));

        $this->mailer->send($email);

        $this->logger->info(__CLASS__ . ": Email type '{$email_type}' with subject '{$email->getSubject()}' is sent successfully");
    }
}
