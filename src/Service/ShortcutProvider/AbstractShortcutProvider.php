<?php declare(strict_types=1);
namespace App\Service\ShortcutProvider;

use App\Models\ShortcutAndUrl\ShortUrlRequest;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractShortcutProvider
{
    function __construct(
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger
    ){}


    abstract function shortcutPattern():string;

    abstract function getShortcut(ShortUrlRequest $app_shortcut_request): ShortUrlRequest;


    protected function isUniqueShortcut(ShortUrlRequest $app_shortcut_request): bool
    {

        return (bool) $this->em->getConnection()->fetchOne('
                SELECT s.id
                FROM shortcuts_urls AS s
                WHERE s.app_domain_id = :app_domain_id
                AND s.generated_shortcut = :shortcut 
                OR s.customer_shortcut = :shortcut
        ',
            [
            'app_domain_id' => $app_shortcut_request->app_domain->id,
            'shortcut' => $app_shortcut_request->shortcut
            ]
        );
    }
}