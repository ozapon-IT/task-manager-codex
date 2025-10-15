COMPOSE ?= docker compose
ENV_FILE ?= .env

.PHONY: setup up down destroy logs backend-shell frontend-shell mysql-shell

setup:
	@echo "Setting up Task Manager development environment"
	@[ -f $(ENV_FILE) ] || (cp .env.example $(ENV_FILE) && echo "Created $(ENV_FILE) from template")
	$(COMPOSE) pull --ignore-pull-failures
	$(COMPOSE) build
	@if [ -f backend/composer.json ]; then \
		echo "Installing PHP dependencies"; \
		$(COMPOSE) run --rm backend composer install; \
	else \
		echo "Laravel skeleton not found in backend/. Run '$(COMPOSE) run --rm backend composer create-project laravel/laravel .'."; \
	fi
	@if [ -f frontend/package.json ]; then \
		echo "Installing Node dependencies"; \
		$(COMPOSE) run --rm frontend npm install; \
	fi

up:
	$(COMPOSE) up -d

up-dev:
	$(COMPOSE) up

down:
	$(COMPOSE) down

logs:
	$(COMPOSE) logs -f

backend-shell:
	$(COMPOSE) run --rm backend bash

frontend-shell:
	$(COMPOSE) run --rm frontend sh

mysql-shell:
	$(COMPOSE) exec mysql mysql -u$${DB_USERNAME:-task_manager} -p$${DB_PASSWORD:-secret}

destroy:
	$(COMPOSE) down -v
