<?php

namespace App\Tests\Command;

use App\Command\FetchCustomersFromCustomersApiCommand;
use App\Service\CustomerImporterService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class FetchCustomersFromCustomersApiCommandTest extends TestCase
{
    private $customerImporterServiceMock;

    protected function setUp(): void
    {
        $this->customerImporterServiceMock = $this->createMock(CustomerImporterService::class);
    }

    public function testExecuteSuccess()
    {
        // Mock CustomerImporterService to return true
        $this->customerImporterServiceMock->method('import')
            ->willReturn(true);

        // Create command instance with mocked service
        $command = new FetchCustomersFromCustomersApiCommand($this->customerImporterServiceMock);

        // Use Application to add command
        $application = new Application();
        $application->add($command);

        // Use CommandTester to run the command
        $commandInstance = $application->find('app:fetch-customers-from-customers-api');
        $commandTester = new CommandTester($commandInstance);

        // Execute the command
        $commandTester->execute([]);

        // Check command output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Command run successfully.', $output);
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testExecuteFailure()
    {
        // Mock CustomerImporterService to return false
        $this->customerImporterServiceMock->method('import')
            ->willReturn(false);

        // Create command instance with mocked service
        $command = new FetchCustomersFromCustomersApiCommand($this->customerImporterServiceMock);

        // Use Application to add command
        $application = new Application();
        $application->add($command);

        // Use CommandTester to run the command
        $commandInstance = $application->find('app:fetch-customers-from-customers-api');
        $commandTester = new CommandTester($commandInstance);

        // Execute the command
        $commandTester->execute([]);

        // Check command output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Command failed.', $output);
        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }
}
