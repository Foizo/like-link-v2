<?php declare(strict_types=1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends Command
{

    protected SymfonyStyle $io;
    protected SymfonyStyle $error_io;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);

        $error_output = $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;

        $this->error_io = new SymfonyStyle($input, $error_output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        return self::FAILURE;
    }
}