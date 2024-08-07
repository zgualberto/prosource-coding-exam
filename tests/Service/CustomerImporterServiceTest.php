<?php

namespace App\Tests\Service;

use App\Repository\CustomerRepository;
use App\Service\CustomerImporterService;
use App\Utils\Http;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CustomerImporterServiceTest extends TestCase
{
    private $customerRepositoryMock;
    private $httpMock;
    private $customerImporterService;

    protected function setUp(): void
    {
        $this->customerRepositoryMock = $this->createMock(CustomerRepository::class);
        $this->httpMock = $this->createMock(Http::class);
        $this->customerImporterService = new CustomerImporterService($this->customerRepositoryMock, $this->httpMock);
    }

    public function testImportSuccess()
    {
        // Mock environment variables
        $_ENV['APP_CUSTOMER_API_URL'] = 'http://example.com';
        $_ENV['APP_CUSTOMER_MAX_FETCH_RESULT'] = 10;

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

        // Expect storeOrUpdate to be called once with specific parameters
        $this->customerRepositoryMock->expects($this->once())
            ->method('storeOrUpdate')
            ->with([
                'full_name' => 'Terra Powell',
                'email' => 'terra.powell@example.com',
                'username' => 'lazygorilla707',
                'password' => '0.0.000',
                'gender' => 'female',
                'country' => 'Australia',
                'city' => 'Bowral',
                'phone' => '00-6142-2839',
            ]);

        // Call the import method
        $result = $this->customerImporterService->import();

        // Assert the result is true
        $this->assertTrue($result);
    }

    public function testImportFailure()
    {
        // Mock environment variables
        $_ENV['APP_CUSTOMER_API_URL'] = 'http://example.com';

        // Create request and response mocks
        $request = new Request('GET', 'test');
        $response = new Response(500); // Simulate server error

        // Mock ClientException
        $this->httpMock->expects($this->once())
            ->method('get')
            ->will($this->throwException(new ClientException('Error', $request, $response)));

        // Expect storeOrUpdate not to be called
        $this->customerRepositoryMock->expects($this->never())
            ->method('storeOrUpdate');

        // Call the import method
        $result = $this->customerImporterService->import();

        // Assert the result is false
        $this->assertFalse($result);
    }
}
