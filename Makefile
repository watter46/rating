up:
	docker compose up -d
build:
	docker compose build --no-cache --force-rm
laravel-install:
	docker compose exec app composer create-project --prefer-dist laravel/laravel . "10.*"
create:
	mkdir -p src
	@make build
	@make up
	@make laravel-install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan storage:link
	docker compose exec app chmod -R 777 storage bootstrap/cache
	@make fresh
init:
	@make build
	@make up
remake:
	@make destroy
	@make init
stop:
	docker compose stop
down:
	docker compose down --remove-orphans
restart:
	@make down
	@make up
destroy:
	docker compose down --rmi all --volumes --remove-orphans
destroy-volumes:
	docker compose down --volumes --remove-orphans
ps:
	docker ps -a --no-trunc
load:
	docker compose exec app composer dump-autoload 
logs:
	docker compose logs
logs-watch:
	docker compose logs --follow
log-web:
	docker compose logs web
log-web-watch:
	docker compose logs --follow web
log-app:
	docker compose logs app
log-app-watch:
	docker compose logs --follow app
log-db:
	docker compose logs db
log-db-testing:
	docker compose logs db-testing
log-redis:
	docker compose logs redis
log-db-watch:
	docker compose logs --follow db
web:
	docker compose exec web ash
app:
	docker compose exec app bash
migrate:
	docker compose exec app php artisan migrate
migrate-test:
	docker compose exec app php artisan migrate --env=testing
fresh:
	docker compose exec app php artisan migrate:fresh --seed
fresh-test:
	docker compose exec app php artisan migrate:fresh --seed --env=testing
seed:
	docker compose exec app php artisan db:seed
dacapo:
	docker compose exec app php artisan dacapo
rollback-test:
	docker compose exec app php artisan migrate:fresh
	docker compose exec app php artisan migrate:refresh
tinker:
	docker compose exec app php artisan tinker
test:
	docker compose exec app composer test
unit:
	docker compose exec app php artisan test tests/Unit
test-clear:
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan key:generate --env=testing
optimize:
	docker compose exec app php artisan optimize
optimize-clear:
	docker compose exec app php artisan optimize:clear
clear:
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:cache
cache:
	docker compose exec app composer dump-autoload -o
	@make optimize
	docker compose exec app php artisan event:cache
	docker compose exec app php artisan view:cache
cache-clear:
	docker compose exec app composer clear-cache
	@make optimize-clear
	docker compose exec app php artisan event:clear
db:
	docker compose exec db bash
db-test:
	docker compose exec db-testing bash
redis:
	docker compose exec redis ash
sql:
	docker compose exec db bash -c 'mysql -u$$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE'
sql-test:
	docker compose exec db-testing bash -c 'mysql -u$$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE'
watch:
	docker compose exec app npm run watch
dev:
	docker compose exec app npm run dev
work:
	docker compose exec app php artisan schedule:work
run:
	docker compose exec app php artisan schedule:run
queue:
	docker compose exec app php artisan queue:listen
stan:
	docker compose exec app ./vendor/bin/phpstan analyse