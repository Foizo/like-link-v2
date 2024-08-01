<?php declare(strict_types=1);
namespace App\Command;

use App\Doctrine\Repository\AppDomainsRepository;
use App\Models\ShortcutAndUrl\ShortUrlRequest;
use App\Service\ShortcutAndUrlManager\ShortUrlManager;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:short-url',
    description: 'Short given URL and return redirect link with shortcut'
)]
class ShortUrlCommand extends AbstractCommand
{
    function __construct(
        protected ShortUrlManager      $short_url_creator,
        protected AppDomainsRepository $domains_repo
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'URL address to short');
        $this->addArgument('domain-identifier', InputArgument::REQUIRED, 'Create shortcut for app domain');
        $this->addArgument('custom-shortcut', InputArgument::OPTIONAL, 'Custom shortcut');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url_to_short = $input->getArgument('url');
        if (!$url_to_short) {
            throw new InvalidArgumentException("URL argument value missing.");
        }

        $domain_identifier = $input->getArgument('domain-identifier');
        if (!$domain_identifier) {
            throw new InvalidArgumentException("Domain identifier argument value missing.");
        }

        $app_domain = $this->domains_repo->findByIdentifier($domain_identifier);
        if (!$app_domain) {
            throw new InvalidArgumentException("App domain with '{$domain_identifier}' identifier not exist.");
        }

        $customer_shortcut = $input->getArgument('custom-shortcut');

        $short_url_request = new ShortUrlRequest();
        $short_url_request->destination_url = $url_to_short;
        $short_url_request->createDestinationUrlHash();
        if ($customer_shortcut) {
            $short_url_request->shortcut = $customer_shortcut;
            $short_url_request->customer_shortcut = true;
        }

        try {
            $this->short_url_creator->shortUrl($short_url_request);
        } catch (Throwable $e) {
            $output->writeln($e->getMessage());
        }

        return self::SUCCESS;
    }
}
