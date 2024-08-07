<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @return Customer[] Returns an array of all Customer objects
     */
    public function findAllOrderById(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneById(int $value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByEmail(String $value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function storeOrUpdate($data): ?Customer
    {
        $manager = $this->getEntityManager();

        $customer = self::findOneByEmail($data['email']) ?? new Customer();
        
        $customer->setFullName($data['full_name']);
        $customer->setEmail($data['email']);
        $customer->setUsername($data['username']);
        $customer->setPassword($data['password']);
        $customer->setGender($data['gender']);
        $customer->setCountry($data['country']);
        $customer->setCity($data['city']);
        $customer->setPhone($data['phone']);

        $manager->persist($customer);
        $manager->flush();

        return $customer;
    }
}
