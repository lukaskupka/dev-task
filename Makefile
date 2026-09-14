.PHONY: help up stop down restart bash comin comdu logs fixtures db-reset

help: ## Print help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"} \
        /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-25s\033[0m %s\n", $$1, $$2 } \
        /^# Section:/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 11) }' $(MAKEFILE_LIST)

# Section: Build
up: backend/.env.local frontend/.env.local ## Build image and start containers
	@mkdir -p backend/var backend/logs backend/temp
	@chmod 777 backend/var backend/logs backend/temp
	$(DOCKER_COMPOSE) build
	$(DOCKER_COMPOSE) up -d --force-recreate --remove-orphans
	$(BASH) -c 'composer install --no-interaction --ignore-platform-reqs'
	@(echo "\033[32m👍 developer-task is running on $(APP_HOST)\033[0m")

stop: ## Stop containers
	$(DOCKER_COMPOSE) stop

down: ## Stop and remove containers
	$(DOCKER_COMPOSE) down

restart: ## Recreate app container (e.g. to apply new ENV variables)
	$(DOCKER_COMPOSE) up $(APP_CONTAINER) -d --force-recreate

# Section: Development tools
bash: checkAppIsRunning ## Open bash inside the app container
	$(BASH)

comin: checkAppIsRunning ## Run composer install
	@$(BASH) -c 'composer install --ignore-platform-reqs'

comdu: checkAppIsRunning ## Run composer dump-autoload
	@$(BASH) -c 'composer dump-autoload'

logs: checkAppIsRunning ## Tail app container logs
	$(DOCKER_COMPOSE) logs -f $(APP_CONTAINER)

fixtures: checkAppIsRunning ## Load database fixtures (purges DB first)
	@$(BASH) -c 'php bin/console fixtures:load'

db-reset: checkAppIsRunning ## Reset DB: run migrations and reload fixtures
	@$(BASH) -c 'php bin/console migrations:migrate --no-interaction'
	@$(BASH) -c 'php bin/console fixtures:load'

backend/.env.local:
	@sed -e "s/{MAKEFILE_UID}/$(shell id -u)/g" \
		-e "s/{MAKEFILE_GID}/$(shell id -g)/g" backend/.env.local.example > backend/.env.local
	@echo "\033[32m👍 backend/.env.local created from backend/.env.local.example\033[0m"

frontend/.env.local:
	@cp frontend/.env.local.example frontend/.env.local
	@echo "\033[32m👍 frontend/.env.local created from frontend/.env.local.example\033[0m"

checkAppIsRunning:
	@if [ "$(APP_CONTAINER_STATUS)" != "true" ]; then \
		echo "\033[31m👎 The '$(APP_CONTAINER)' container is not running. Try 'make up' first.\033[0m"; \
		exit 1; \
	fi

-include backend/.env.local
export $(shell test -f backend/.env.local && sed 's/=.*//' backend/.env.local)

export APP_HOST := http://localhost:3000

MAKEFILE_UID ?= $(shell id -u)
MAKEFILE_GID ?= $(shell id -g)
export MAKEFILE_UID
export MAKEFILE_GID

COMPOSE_FILE ?= docker-compose.local.yml
DOCKER_COMPOSE = docker compose -f docker-compose.yml -f $(COMPOSE_FILE)
APP_CONTAINER = app
FRONTEND_CONTAINER = frontend
BASH = $(DOCKER_COMPOSE) exec $(APP_CONTAINER) bash
FRONTEND_BASH = $(DOCKER_COMPOSE) exec $(FRONTEND_CONTAINER) sh
APP_CONTAINER_STATUS = $(shell docker inspect -f '{{.State.Running}}' $$(docker compose ps -q $(APP_CONTAINER)) 2>/dev/null)

%:
	@:
