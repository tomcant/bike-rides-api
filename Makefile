.PHONY: *
.DEFAULT_GOAL := help

SHELL := /bin/bash
COMPOSE := docker compose -f docker/docker-compose.yml -p bike-rides-api
APP := $(COMPOSE) exec -T app-rides

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
	@$(APP) bin/console doctrine:database:create -n --env $*
	@$(APP) bin/console doctrine:migrations:migrate -n --allow-no-migration --env $*

##@ Testing/Linting

can-release: security lint test ## Check the application is releasable

test: db/test ## Run the test suite
	$(APP) bin/phpunit --log-junit /var/reports/phpunit.xml

test/application: ## Run the application test suite
	$(APP) bin/phpunit --testsuite application

test/infrastructure: db/test ## Run the infrastructure test suite
	$(APP) bin/phpunit --testsuite infrastructure

test/ui: db/test ## Run the UI test suite
	$(APP) bin/phpunit --testsuite ui

test/%:
	$(APP) bin/phpunit --filter $*

lint: ## Run the linting tools
	$(APP) composer validate --strict
	$(APP) php-cs-fixer fix --dry-run --diff

security: ## Check dependencies for known vulnerabilities
	$(APP) local-php-security-checker

fmt: format
format: ## Fix style related code violations
	$(APP) php-cs-fixer fix

##@ Fixtures

fixture/bike: ## Create and activate a bike
	@$(COMPOSE) exec app-rides bash -c "TERM=xterm-256color bin/console fixture:bike"

##@ Running Instance

open-api: ## Open the API in the default browser
	open "http://localhost:8000/"

shell: ## Access a shell on the running container
	$(COMPOSE) exec app-rides bash

logs: ## Tail the container logs
	$(COMPOSE) logs -f

ps: ## List the running containers
	$(COMPOSE) ps

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
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-\/\/]+:.*?##/ { printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
