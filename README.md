## Calliopen

Open source music project.

## How to run locally?

To run the project you must follow these commands:
```bash
npm install

cp .env.example .env
docker compose up -d
docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate:fresh --seed
```