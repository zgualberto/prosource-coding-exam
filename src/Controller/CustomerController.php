<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CustomerController extends AbstractController
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    #[Route('/customers', name: 'app_customer_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $customers = $this->customerRepository->findAllOrderById();

        $data = array_map(function ($customer) {
            return [
                'full_name' => $customer->getFullName(),
                'email' => $customer->getEmail(),
                'country' => $customer->getCountry(),
            ];
        }, $customers);

        return $this->json($data);
    }

    #[Route('/customers/{customerId}', name: 'app_customer_show', methods: ['GET'])]
    public function showCustomer(int $customerId): JsonResponse
    {
        $customer = $this->customerRepository->findOneById($customerId);

        if (!$customer) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $data = [
            'full_name' => $customer->getFullName(),
            'email' => $customer->getEmail(),
            'username' => $customer->getUsername(),
            'gender' => $customer->getGender(),
            'country' => $customer->getCountry(),
            'city' => $customer->getCity(),
            'phone' => $customer->getPhone(),
        ];

        return $this->json($data);
    }
}
