## Setup

### Pre-requisites

  * PHP 8.3
  * Composer

### Steps
1. Create `.env` from `.env.example`
2. Run migrations `php bin/console doctrine:migrations:migrate` (run it twice for `test.db` after updating `.env`)
3. To run unit test `php bin/phpunit`
4. To fetch customers from API `php bin/console app:fetch-customers-from-customers-api`

### Endpoints
* GET /customers
* GET /customers/:id