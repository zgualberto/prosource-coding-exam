<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Customer;
use App\Repository\CustomerRepository;

class CustomerControllerTest extends WebTestCase
{
    private $customerRepositoryMock;

    protected function setUp(): void
    {
        $this->customerRepositoryMock = $this->createMock(CustomerRepository::class);
    }

    private function createClientWithMockedRepository()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set('App\Repository\CustomerRepository', $this->customerRepositoryMock);
        return $client;
    }

    public function testIndex()
    {
        $customer = new Customer();
        $customer->setFullName('John Doe')
            ->setEmail('john@example.com')
            ->setCountry('Australia');

        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('findAllOrderById')
            ->willReturn([$customer]);

        $client = $this->createClientWithMockedRepository();
        $client->request('GET', '/customers');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(1, $content);
        $this->assertEquals('John Doe', $content[0]['full_name']);
        $this->assertEquals('john@example.com', $content[0]['email']);
        $this->assertEquals('Australia', $content[0]['country']);
    }

    public function testShowCustomer()
    {
        $customer = new Customer();
        $customer->setFullName('Jane Doe')
            ->setEmail('jane@example.com')
            ->setUsername('janedoe')
            ->setGender('female')
            ->setCountry('Australia')
            ->setCity('Sydney')
            ->setPhone('1234567890');

        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('findOneById')
            ->with(1)
            ->willReturn($customer);

        $client = $this->createClientWithMockedRepository();
        $client->request('GET', '/customers/1');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Jane Doe', $content['full_name']);
        $this->assertEquals('jane@example.com', $content['email']);
        $this->assertEquals('janedoe', $content['username']);
        $this->assertEquals('female', $content['gender']);
        $this->assertEquals('Australia', $content['country']);
        $this->assertEquals('Sydney', $content['city']);
        $this->assertEquals('1234567890', $content['phone']);
    }

    public function testShowCustomerNotFound()
    {
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('findOneById')
            ->with(999)
            ->willReturn(null);

        $client = $this->createClientWithMockedRepository();
        $client->request('GET', '/customers/999');

        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($client->getResponse()->getContent());

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Not found', $content['error']);
    }
}
