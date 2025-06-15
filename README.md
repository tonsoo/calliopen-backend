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

## Api

The api docs are specified int the file [client-openapi.yaml](storage/api-docs/client-openapi.yaml).

To access API documentation you can open https://calliopen.com.br/api/documentation or http://localhost:9912/api/documentation (in case you have your docker container up).