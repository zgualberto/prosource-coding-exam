<?php

namespace App\Command;

use App\Utils\Http;
use App\Repository\CustomerRepository;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fetch-customers-from-customers-api')]
class FetchCustomersFromCustomersApiCommand extends Command
{
    private $customerRepository;
    private $http;

    public function __construct(CustomerRepository $customerRepository, Http $http)
    {
        parent::__construct();
        $this->customerRepository = $customerRepository;
        $this->http = $http;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('This command will populate customer table');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
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
        } catch (ClientException $e) {
            $output->writeln('Command failed.');
            return Command::FAILURE;
        }

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

        $output->writeln('Command run successfully.');
        return Command::SUCCESS;
    }
}
