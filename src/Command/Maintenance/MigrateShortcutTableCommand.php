<?php declare(strict_types=1);
namespace App\Command\Maintenance;

use App\Command\AbstractCommand;
use App\Doctrine\Entity\CustomerUrl;
use App\Doctrine\Repository\CustomerUrlsRepository;
use App\Enums\Shortcut\CustomerShortcut;
use App\Enums\Shortcut\GeneratedShortcut;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'maintenance:migrate-shortcut-table',
    description: 'Migrate values from Shortcut to Customer table'
)]
class MigrateShortcutTableCommand extends AbstractCommand
{
    function __construct(
        protected CustomerUrlsRepository $customer_url_repo
    ){
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->addOption(name: 'dry-run',mode: InputOption::VALUE_NONE, description: 'Dry run');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dry_run = $input->getOption('dry-run');

        $all_customer_urls = $this->customer_url_repo->findAll();
        $count = count($all_customer_urls);

        $nr=0;
        foreach (array_chunk($all_customer_urls, 1000) as $batch_customer_urls) {

            /** @var CustomerUrl $customer_url */
            foreach ($batch_customer_urls as $customer_url) {
                $nr++;
                $output->writeln("[{$nr}/{$count}] Migrating values for CustomerURL #{$customer_url->id}");
                if (!$dry_run) {
                    $shortcut_url = $customer_url->shortcut_url;
                    $customer_url->shortcuts->generated_shortcut = $shortcut_url->generated_shortcut === GeneratedShortcut::NOT_GENERATED ? null : $shortcut_url->generated_shortcut;
                    $customer_url->shortcuts->customer_shortcut = $shortcut_url->customer_shortcut === CustomerShortcut::NOT_SPECIFIED ? null : $shortcut_url->customer_shortcut;
                }
            }

            $this->customer_url_repo->saveMultiple(entities: $batch_customer_urls, in_transaction: true);
        }

        return self::SUCCESS;
    }
}
