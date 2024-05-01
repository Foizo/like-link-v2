<?php declare(strict_types=1);
namespace App\Service\ShortcutProvider;

use App\Doctrine\Entity\AppDomain;
use App\Models\ShortcutAndUrl\ShortUrlRequest;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractShortcutProvider
{
    function __construct(
        protected AppDomain $current_app,
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger
    ){}


    abstract function shortcutPattern():string;

    abstract function getShortcut(ShortUrlRequest $short_url_request): ShortUrlRequest;


    protected function isExistUniqueShortcut(ShortUrlRequest $shor_url_request): bool
    {

        return (bool) $this->em->getConnection()->fetchOne('
                SELECT u.id
                FROM customer_urls AS u
                WHERE u.app_domain_id = :app_domain_id
                AND u.shortcuts_generated_shortcut = :shortcut 
                OR u.shortcuts_customer_shortcut = :shortcut
        ',
            [
            'app_domain_id' => $this->current_app->id,
            'shortcut' => $shor_url_request->shortcut
            ]
        );
    }
}