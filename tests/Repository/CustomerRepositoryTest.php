<?php

namespace App\Tests\Repository;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CustomerRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Closing the entity manager to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    public function testFindAllOrderById()
    {
        $customer = new Customer();
        $customer->setFullName('John Doe')
            ->setEmail('john@example.com')
            ->setUsername('johndoe')
            ->setPassword('password')
            ->setGender('male')
            ->setCountry('USA')
            ->setCity('New York')
            ->setPhone('1234567890');

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $customers = $customerRepository->findAllOrderById();

        $this->assertCount(1, $customers);
        $this->assertEquals('John Doe', $customers[0]->getFullName());
    }

    public function testFindOneById()
    {
        $customer = new Customer();
        $customer->setFullName('Jane Doe')
            ->setEmail('jane@example.com')
            ->setUsername('janedoe')
            ->setPassword('password')
            ->setGender('female')
            ->setCountry('USA')
            ->setCity('Los Angeles')
            ->setPhone('0987654321');

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $foundCustomer = $customerRepository->findOneById($customer->getId());

        $this->assertNotNull($foundCustomer);
        $this->assertEquals('Jane Doe', $foundCustomer->getFullName());
    }

    public function testFindOneByEmail()
    {
        $customer = new Customer();
        $customer->setFullName('Alice Doe')
            ->setEmail('alice@example.com')
            ->setUsername('alicedoe')
            ->setPassword('password')
            ->setGender('female')
            ->setCountry('USA')
            ->setCity('San Francisco')
            ->setPhone('1231231234');

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $foundCustomer = $customerRepository->findOneByEmail('alice@example.com');

        $this->assertNotNull($foundCustomer);
        $this->assertEquals('Alice Doe', $foundCustomer->getFullName());
    }

    public function testStoreOrUpdate()
    {
        $data = [
            'full_name' => 'Bob Doe',
            'email' => 'bob@example.com',
            'username' => 'bobdoe',
            'password' => 'password',
            'gender' => 'male',
            'country' => 'Canada',
            'city' => 'Toronto',
            'phone' => '3213213210'
        ];

        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $customer = $customerRepository->storeOrUpdate($data);

        $this->assertNotNull($customer);
        $this->assertEquals('Bob Doe', $customer->getFullName());

        // Test updating the existing customer
        $data['full_name'] = 'Robert Doe';
        $updatedCustomer = $customerRepository->storeOrUpdate($data);

        $this->assertNotNull($updatedCustomer);
        $this->assertEquals('Robert Doe', $updatedCustomer->getFullName());
    }
}
