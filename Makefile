.PHONY: *
.DEFAULT_GOAL := help

SHELL := /bin/bash
COMPOSE := docker compose -f docker/docker-compose.yml -p bike-rides-api
APP := $(COMPOSE) exec -T app

##@ Setup

start: up composer db ## Start the application in development mode

start-ext: up-ext composer db ## Start the application in development mode with external access via localtunnel

stop: ## Stop the application and clean up
	$(COMPOSE) down -v --remove-orphans

restart: stop start ## Restart the application in development mode

up:
	$(COMPOSE) up -d --build --force-recreate --remove-orphans

up-ext: ghcr-login
	$(COMPOSE) -f docker/docker-compose.ext.yml up -d --build --force-recreate

composer: ## Install the latest Composer dependencies
	$(APP) composer install --no-interaction

db: db/dev db/test ## (Re)create the development and test databases
db/%:
	@$(APP) bin/console doctrine:database:drop --force --if-exists --env $*
	@$(APP) bin/console doctrine:database:create --no-interaction --env $*
	@$(APP) bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction --env $*

##@ Testing/Linting

can-release: security lint test ## Check the application is releasable

test: db/test ## Run the test suite
	$(APP) vendor/bin/phpunit --log-junit /var/reports/phpunit.xml --order-by=random

test/%:
	$(APP) vendor/bin/phpunit --filter $*

lint: ## Run the linting tools
	$(APP) composer validate --strict
	$(APP) sh -c 'PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --dry-run --diff'
	$(APP) vendor/bin/phpstan analyse --no-interaction

security: ## Check dependencies for known vulnerabilities
	$(APP) composer audit

fmt: format
format: ## Fix style related code violations
	$(APP) sh -c 'PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix'

##@ Fixtures

fixture/bike: ## Create and activate a bike
	@$(COMPOSE) exec app bash -c "TERM=xterm-256color bin/console bikes:fixture:bike"

##@ Running Instance

open: ## Open the API in the default browser
	open "http://localhost:8000/"

shell: ## Access a shell on the running container
	$(COMPOSE) exec app bash

logs: ## Tail the container logs
	$(COMPOSE) logs -f

ps: ## List the running containers
	$(COMPOSE) ps -a

ghcr-login: _require_GHCR_TOKEN
	@NOW="$$(date +%s)"; \
	if [[ "$$NOW" -gt $$(cat .GHCR_NEXT_LOGIN_AFTER 2> /dev/null) ]]; then \
	  echo $$GHCR_TOKEN | docker login ghcr.io -u irrelevant-user --password-stdin 2>&1 >/dev/null; \
	  EXPIRES=$$(( NOW + 300 )); \
	  echo "$$EXPIRES" > .GHCR_NEXT_LOGIN_AFTER; \
	fi;

_require_%:
	@_=$(or $($*),$(error "`$*` env var required"))

help:
	@echo "$$HEADER"
	@awk 'BEGIN {FS = ":.*##"; printf "Usage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_\-\/\/]+:.*?##/ { printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
	@echo

define HEADER
  ____  _ _          ____  _     _                _    ____ ___
 | __ )(_) | _____  |  _ \(_) __| | ___  ___     / \  |  _ \_ _|
 |  _ \| | |/ / _ \ | |_) | |/ _` |/ _ \/ __|   / _ \ | |_) | |
 | |_) | |   <  __/ |  _ <| | (_| |  __/\__ \  / ___ \|  __/| |
 |____/|_|_|\_\___| |_| \_\_|\__,_|\___||___/ /_/   \_\_|  |___|

endef
export HEADER
