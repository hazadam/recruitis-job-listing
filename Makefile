.PHONY: up down be fe ber fer

up:
	@docker compose up -d

down:
	@docker compose down --remove-orphans

be:
	@docker compose exec app-be bash

ber:
	@docker compose exec --user=root app-be bash

fe:
	@docker compose exec app-fe bash

fer:
	@docker compose exec --user=root app-fe bash

dev-be: up
	@docker compose exec --user=root app-be bash -c "composer dev"

dev-fe: up
	@docker compose exec --user=root app-fe bash -c "npm run dev"
