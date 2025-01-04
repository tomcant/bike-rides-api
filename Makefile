.PHONY: *
.DEFAULT_GOAL := help

SHELL := /bin/bash
COMPOSE := docker compose -f docker/docker-compose.yml -p bike-rides-api

##@ Setup

start: up composer db ## Start the application in development mode

stop: ## Stop the application and clean up
	$(COMPOSE) down -v --remove-orphans

restart: stop start ## Restart the application in development mode

up:
	$(COMPOSE) up -d --build --force-recreate --remove-orphans

composer: ## Install the latest Composer dependencies
	@for app in bikes rides billing; do \
	  echo "::group::Install $${app} dependencies"; \
	  $(COMPOSE) exec -T "$${app}-api" composer install --no-interaction; \
	  echo '::endgroup::'; \
	done

db: db/dev db/test ## (Re)create the development and test databases
db/%:
	@for app in bikes rides billing; do \
	  echo "::group::Setup $${app} $* database"; \
	  $(COMPOSE) exec -T "$${app}-api" bin/console doctrine:database:drop --force --if-exists --env $*; \
	  $(COMPOSE) exec -T "$${app}-api" bin/console doctrine:database:create --no-interaction --env $*; \
	  $(COMPOSE) exec -T "$${app}-api" bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction --env $*; \
	  echo '::endgroup::'; \
	done

##@ Testing/Linting

can-release: security test lint ## Check the application is releasable

security: ## Check dependencies for known vulnerabilities
	@for app in bikes rides billing; do \
	  echo "::group::Audit dependencies for $${app}"; \
	  $(COMPOSE) exec -T "$${app}-api" composer audit; \
	  [[ $$? != 0 ]] && { echo "::error::$${app} failed"; failed=1; }; \
	  echo '::endgroup::'; \
	done; \
	exit $${failed:-0};

test: test-apps test-packages ## Run the test suite

lint: lint-apps lint-packages ## Run the linting tools

test-apps: db/test
	@for app in bikes rides billing; do \
	  echo "::group::Test $${app}"; \
	  $(COMPOSE) exec -T "$${app}-api" composer test; \
	  [[ $$? != 0 ]] && { echo "::error::$${app} failed"; failed=1; }; \
	  echo '::endgroup::'; \
	done; \
	exit $${failed:-0};

test-packages:
	@for package in packages/*; do \
	  echo "::group::Test $${package}"; \
	  docker run --rm \
	    -w /app/"$${package}" \
	    -v $(PWD)/packages:/app/packages \
	    bike-rides-api:latest \
	      sh -c 'composer install --no-progress && composer test'; \
	  [[ $$? != 0 ]] && { echo "::error::$${package} failed"; failed=1; }; \
	  echo '::endgroup::'; \
	done; \
	exit $${failed:-0};

lint-apps:
	@for app in bikes rides billing; do \
	  echo "::group::Lint $${app}"; \
	  $(COMPOSE) exec -T "$${app}-api" composer lint; \
	  [[ $$? != 0 ]] && { echo "::error::$${app} failed"; failed=1; }; \
	  echo '::endgroup::'; \
	done; \
	exit $${failed:-0};

lint-packages:
	@for package in packages/*; do \
	  echo "::group::Lint $${package}"; \
	  docker run --rm \
	    -w /app/"$${package}" \
	    -v $(PWD)/packages:/app/packages \
	    bike-rides-api:latest \
	      sh -c 'composer install --no-progress && composer lint'; \
	  [[ $$? != 0 ]] && { echo "::error::$${package} failed"; failed=1; }; \
	  echo '::endgroup::'; \
	done; \
	exit $${failed:-0};

fmt: format
format: ## Fix style related code violations
	@for app in bikes rides billing; do \
	  $(COMPOSE) exec -T "$${app}-api" composer format; \
	done

##@ Fixtures

fixture/bike: ## Create and activate a bike
	@$(COMPOSE) exec bikes-api bash -c 'TERM=xterm-256color bin/console bikes:fixture:bike'

##@ Running Instance

shell/%: ## Access a shell on the running container
	$(COMPOSE) exec $*-api bash

logs: ## Tail the container logs
	$(COMPOSE) logs -f

ps: ## List the running containers
	$(COMPOSE) ps -a

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
