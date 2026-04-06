SHELL := /bin/bash

# ---------------------------------------------------------------------------
# Paths & variables
# ---------------------------------------------------------------------------
ROOT_DIR    := $(CURDIR)
DOCKER_DIR  := $(ROOT_DIR)/docker
COMPOSE     := docker compose -f $(DOCKER_DIR)/docker-compose.yml --env-file $(DOCKER_DIR)/.env
HOST_UID    := $(shell id -u 2>/dev/null || echo 1000)
HOST_GID    := $(shell id -g 2>/dev/null || echo 1000)

# Export UID/GID so compose can resolve ${HOST_UID}/${HOST_GID} from the shell
# even when .env is not yet created (first-time setup).
export HOST_UID HOST_GID

###############################################################################
# HELP
###############################################################################
.DEFAULT_GOAL := help
.PHONY: help
help:
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"}'
	@grep -h -E '^[0-9a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-24s\033[0m %s\n", $$1, $$2}'
	@echo ""

###############################################################################
# SETUP
###############################################################################
.PHONY: setup
setup: init-env build deps-install ## Full first-time project setup

.PHONY: init-env
init-env: ## Copy .env.example → .env / .env.local (no overwrite)
	@cp -n $(DOCKER_DIR)/.env.example $(DOCKER_DIR)/.env 2>/dev/null || true
	@cp -n $(ROOT_DIR)/backend/.env.example $(ROOT_DIR)/backend/.env.local 2>/dev/null || true
	@cp -n $(ROOT_DIR)/frontend/.env.example $(ROOT_DIR)/frontend/.env.local 2>/dev/null || true
	@echo "Environment files ready."

###############################################################################
# DOCKER COMPOSE
###############################################################################
.PHONY: build
build: ## Build all images
	$(COMPOSE) build

.PHONY: build-no-cache
build-no-cache: ## Build all images without cache
	$(COMPOSE) build --no-cache

.PHONY: up
up: ## Start all containers (foreground)
	$(COMPOSE) up

.PHONY: up-d
up-d: ## Start all containers (detached)
	$(COMPOSE) up -d

.PHONY: down
down: ## Stop and remove containers
	$(COMPOSE) down

.PHONY: restart
restart: down up-d ## Restart containers

.PHONY: ps
ps: ## Show container status
	$(COMPOSE) ps

.PHONY: logs
logs: ## Tail logs for all services
	$(COMPOSE) logs -f

###############################################################################
# DEPENDENCY INSTALLATION
###############################################################################
.PHONY: deps-install
deps-install: deps-backend deps-frontend ## Install all dependencies

.PHONY: deps-backend
deps-backend: ## Install backend (Composer) dependencies
	$(COMPOSE) run --rm --user $(HOST_UID):$(HOST_GID) backend composer install

.PHONY: deps-frontend
deps-frontend: ## Install frontend (npm) dependencies
	$(COMPOSE) run --rm --user $(HOST_UID):$(HOST_GID) frontend npm install

###############################################################################
# SHELLS
###############################################################################
.PHONY: sh-backend
sh-backend: ## Shell into backend container
	$(COMPOSE) exec backend /bin/bash

.PHONY: sh-backend-root
sh-backend-root: ## Shell into backend container as root
	$(COMPOSE) exec --user root backend /bin/bash

.PHONY: sh-frontend
sh-frontend: ## Shell into frontend container
	$(COMPOSE) exec --user $(HOST_UID):$(HOST_GID) frontend /bin/bash

.PHONY: sh-frontend-root
sh-frontend-root: ## Shell into frontend container as root
	$(COMPOSE) exec --user root frontend /bin/bash

.PHONY: sh-db
sh-db: ## Shell into database container
	$(COMPOSE) exec db /bin/bash

###############################################################################
# BACKEND – Symfony
###############################################################################
.PHONY: console
console: ## Run Symfony console command (usage: make console CMD="cache:clear")
	$(COMPOSE) exec --user $(HOST_UID):$(HOST_GID) backend php bin/console $(CMD)

.PHONY: db-create
db-create: ## Create database if missing
	$(COMPOSE) exec --user $(HOST_UID):$(HOST_GID) backend php bin/console doctrine:database:create --if-not-exists

.PHONY: migrate
migrate: ## Run Doctrine migrations
	$(COMPOSE) exec --user $(HOST_UID):$(HOST_GID) backend php bin/console doctrine:migrations:migrate -n

###############################################################################
# FRONTEND
###############################################################################
.PHONY: frontend-exec
frontend-exec: ## Run command in frontend (usage: make frontend-exec CMD="npm run build")
	$(COMPOSE) run --rm --user $(HOST_UID):$(HOST_GID) frontend $(CMD)