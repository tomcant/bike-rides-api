.PHONY: *
.DEFAULT_GOAL := help

SHELL := /bin/bash
COMPOSE := docker compose -f .docker/docker-compose.yml -p bike-rides-bikes-api
APP := $(COMPOSE) exec -T app

##@ Setup

start: up composer db ## Start the application in development mode

restart: stop start ## Restart the application in development mode

stop: ## Stop the application and clean up
	$(COMPOSE) down -v --remove-orphans

up:
	$(COMPOSE) up -d --build --force-recreate --remove-orphans

composer: ## Install the latest Composer dependencies
	$(APP) composer install --no-interaction

db: db/dev db/test ## (Re)create the development and test databases
db/%:
	@$(APP) bin/console doctrine:database:drop --force --if-exists --env $*
	@$(APP) bin/console doctrine:database:create --no-interaction --env $*
	@$(APP) bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction --env $*

##@ Testing/Linting

can-release: security test lint ## Run all the same checks as CI to ensure this code will be releasable

security: ## Check dependencies for known vulnerabilities
	$(APP) composer security

test: ## Run the test suite
	$(APP) composer test

lint: ## Run static analysis tools
	$(APP) composer lint

fmt: format
format: ## Fix style related code violations
	$(APP) composer format

##@ Running Instance

fixture/bike: ## Register and activate a bike
	@$(COMPOSE) exec app bash -c 'TERM=xterm-256color bin/console bikes:fixture:bike'

open: ## Open the app in the default browser
	open "http://localhost:8080/"

shell: ## Access a shell on the running container
	$(COMPOSE) exec app bash

logs: ## Tail the container logs
	$(COMPOSE) logs -f

ps: ## List the running containers
	$(COMPOSE) ps -a

_require_%:
	@_=$(or $($*),$(error "`$*` env var required"))

help:
	@echo "$$HEADER"
	@awk 'BEGIN {FS = ":.*##"; printf "Usage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_\-\/\/]+:.*?##/ { printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
	@echo

define HEADER
  ____  _ _                  _    ____ ___
 | __ )(_) | _____  ___     / \  |  _ \_ _|
 |  _ \| | |/ / _ \/ __|   / _ \ | |_) | |
 | |_) | |   <  __/\__ \  / ___ \|  __/| |
 |____/|_|_|\_\___||___/ /_/   \_\_|  |___|

endef
export HEADER
