<?php

namespace App\Tests\Command;

use App\Command\FetchCustomersFromCustomersApiCommand;
use App\Repository\CustomerRepository;
use App\Utils\Http;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class FetchCustomersFromCustomersApiCommandTest extends TestCase
{
    private $customerRepositoryMock;
    private $httpMock;

    protected function setUp(): void
    {
        $this->customerRepositoryMock = $this->createMock(CustomerRepository::class);
        $this->httpMock = $this->createMock(Http::class);
    }

    public function testExecuteSuccess()
    {
        // Mock environment variables
        putenv('APP_CUSTOMER_API_URL=http://example.com');
        putenv('APP_CUSTOMER_MAX_FETCH_RESULT=10');

        // Mock API response
        $apiResponse = new Response(200, [], json_encode([
            'results' => [
                [
                    'name' => ['first' => 'Terra', 'last' => 'Powell'],
                    'email' => 'terra.powell@example.com',
                    'login' => ['username' => 'lazygorilla707', 'password' => '0.0.000'],
                    'gender' => 'female',
                    'location' => ['country' => 'Australia', 'city' => 'Bowral'],
                    'phone' => '00-6142-2839',
                    'nat' => 'AU'
                ]
            ]
        ]));

        // Set up Http mock to return the mocked response
        $this->httpMock->expects($this->once())
            ->method('get')
            ->willReturn($apiResponse);

        // Mock storeOrUpdate to accept any parameter and return true
        $this->customerRepositoryMock->expects($this->once())
            ->method('storeOrUpdate')
            ->with($this->isType('array'));

        // Create command instance with mocked CustomerRepository and Http
        $command = new FetchCustomersFromCustomersApiCommand($this->customerRepositoryMock, $this->httpMock);

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
    }

    public function testExecuteFailsWithClientException()
    {
        // Mock environment variables
        putenv('APP_CUSTOMER_API_URL=http://example.com');

        // Create request and response mocks
        $request = new Request('GET', 'test');
        $response = new Response(500); // Simulate server error

        // Mock ClientException
        $this->httpMock->expects($this->once())
            ->method('get')
            ->will($this->throwException(new ClientException('Error', $request, $response)));

        // Ensure storeOrUpdate is not called
        $this->customerRepositoryMock->expects($this->never())
            ->method('storeOrUpdate');

        // Create command instance with mocked CustomerRepository and Http
        $command = new FetchCustomersFromCustomersApiCommand($this->customerRepositoryMock, $this->httpMock);

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
    }
}
