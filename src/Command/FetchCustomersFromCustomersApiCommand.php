<?php

namespace App\Command;

use App\Service\CustomerImporterService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fetch-customers-from-customers-api')]
class FetchCustomersFromCustomersApiCommand extends Command
{
    private $customerImporterService;

    public function __construct(CustomerImporterService $customerImporterService)
    {
        parent::__construct();
        $this->customerImporterService = $customerImporterService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('This command will populate customer table');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $result = $this->customerImporterService->import();

        if ($result) {
            $output->writeln('Command run successfully.');
            return Command::SUCCESS;
        } else {
            $output->writeln('Command failed.');
            return Command::FAILURE;
        }
    }
}
