<?php

namespace App\Service;

use App\Repository\CustomerRepository;
use App\Utils\Http;

class CustomerImporterService
{
    private $customerRepository;
    private $http;

    public function __construct(CustomerRepository $customerRepository, Http $http)
    {
        $this->customerRepository = $customerRepository;
        $this->http = $http;
    }

    public function import()
    {
        try {
            $response = $this->http->get($_ENV['APP_CUSTOMER_API_URL'], [
                'query' => [
                    'results' => $_ENV['APP_CUSTOMER_MAX_FETCH_RESULT'] ?? 100
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $customers = json_decode($response->getBody());

            if (count($customers->results) > 0) {
                foreach ($customers->results as $customer) {
                    if ($customer->nat == 'AU') {
                        $this->customerRepository->storeOrUpdate([
                            'full_name' => $customer->name->first . ' ' . $customer->name->last,
                            'email' => $customer->email,
                            'username' => $customer->login->username,
                            'password' => $customer->login->password,
                            'gender' => $customer->gender,
                            'country' => $customer->location->country,
                            'city' => $customer->location->city,
                            'phone' => $customer->phone,
                        ]);
                    }
                }
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
