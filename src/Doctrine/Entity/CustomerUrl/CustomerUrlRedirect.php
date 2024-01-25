<?php declare(strict_types=1);
namespace App\Doctrine\Entity\CustomerUrl;

use App\Doctrine\Entity\Common\DefaultEntity;
use App\Doctrine\Entity\Common\Traits\RelatedDomainTrait;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Entity\CustomerUrl\Parts\CustomerUrlRedirectParameters;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'customer_urls_redirects')]

#[ORM\Index(columns: ['redirect_date'], name: 'customer_url_redirect_date_idx')]
class CustomerUrlRedirect extends DefaultEntity
{
    use RelatedDomainTrait;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?CustomerUrl $customer_url = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public DateTime $redirect_date;

    #[ORM\Embedded]
    public CustomerUrlRedirectParameters $params;

    function __construct()
    {
        parent::__construct();
        $this->redirect_date = new DateTime();
        $this->params = new CustomerUrlRedirectParameters();
    }
}