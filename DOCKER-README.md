# Notes – Docker Development Environment

## Architecture Overview

The project runs as a set of Docker containers orchestrated by Docker Compose. All infrastructure configuration lives under the `docker/` directory, while each application (backend, frontend) manages its own environment independently.

```
.
├── docker/
│   ├── docker-compose.yml      # Service definitions
│   ├── .env.example            # Infrastructure variables template
│   ├── backend/
│   │   └── Dockerfile          # PHP 8.4 CLI image (Symfony 8.0)
│   └── frontend/
│       └── Dockerfile          # Node 24 image (Vue.js 3 / Vite)
├── backend/
│   ├── .env                    # Symfony base config (committed)
│   └── .env.example            # Template for .env.local (secrets)
├── frontend/
│   └── .env.example            # Template for .env.local
└── Makefile                    # Developer-facing commands
```

### Services

| Service | Image | Internal Port | Default Host Port |
|---|---|---|---|
| **frontend** | Node 24 (bookworm-slim) | 5173 | `localhost:5173` |
| **backend** | PHP 8.4 CLI (bookworm) | 8880 | `localhost:8880` |
| **db** | MariaDB 11.8 | 3306 | `localhost:3307` |
| **phpmyadmin** | phpMyAdmin 5 | 80 | `localhost:8881` |

---

## Quick Start

```bash
# 1. Clone the repository
git clone <repo-url> repo-name && cd repo-name

# 2. Full first-time setup (copies env files, builds images, installs dependencies)
make setup

# 3. Start the stack
make up
```

The application is now available at:

- **Frontend (Vite dev server):** http://localhost:5173
- **Backend (Symfony):** http://localhost:8880
- **phpMyAdmin:** http://localhost:8881

---

## Environment Variables

The project uses a **three-layer `.env` strategy** to avoid duplication and keep secrets out of `docker-compose.yml`.

### Layer 1 – Docker infrastructure (`docker/.env`)

Controls ports, container names, database credentials, and host UID/GID. Shared across all services that need infrastructure-level configuration (db, phpmyadmin).

```bash
# Created from template – never committed to VCS
cp docker/.env.example docker/.env
```

| Variable | Default | Description |
|---|---|---|
| `COMPOSE_PROJECT_NAME` | `notes` | Prefix for container & volume names |
| `HOST_UID` | `1000` | Host user ID (file ownership sync) |
| `HOST_GID` | `1000` | Host group ID (file ownership sync) |
| `BACKEND_PORT` | `8880` | Backend exposed port |
| `FRONTEND_PORT` | `5173` | Frontend exposed port |
| `DB_PORT` | `3307` | Database exposed port |
| `PMA_PORT` | `8881` | phpMyAdmin exposed port |
| `DB_NAME` | `notes` | Database name |
| `DB_USER` | `app` | Database user |
| `DB_PASSWORD` | `app` | Database password |
| `DB_ROOT_PASSWORD` | `root` | Database root password |

### Layer 2 – Backend application (`backend/.env.local`)

Symfony-specific secrets and connection strings. The base `backend/.env` (committed) sets `APP_ENV=dev` and `APP_DEBUG=1`. Local overrides go into `.env.local`:

```bash
cp backend/.env.example backend/.env.local
```

| Variable | Description |
|---|---|
| `APP_SECRET` | Symfony application secret (change to a random hex string) |
| `DATABASE_URL` | Doctrine DBAL connection string (must match `DB_*` values from `docker/.env`) |

### Layer 3 – Frontend application (`frontend/.env.local`)

Vite exposes variables prefixed with `VITE_` to the browser:

```bash
cp frontend/.env.example frontend/.env.local
```

| Variable | Default | Description |
|---|---|---|
| `VITE_API_BASE_URL` | `http://localhost:8880` | Backend API URL as seen from the browser |

> **Note:** `make init-env` (or `make setup`) copies all three `.env.example` files automatically without overwriting existing ones.

---

## Makefile Targets

Run `make` or `make help` to see the full list. Key targets:

### Setup & Build

| Target | Description |
|---|---|
| `make setup` | One-command first-time setup (env + build + deps) |
| `make init-env` | Copy `.env.example` → `.env` / `.env.local` (no overwrite) |
| `make build` | Build all Docker images |
| `make build-no-cache` | Rebuild all images without cache |

### Container Lifecycle

| Target | Description |
|---|---|
| `make up` | Start all containers (foreground, logs visible) |
| `make up-d` | Start all containers (detached) |
| `make down` | Stop and remove containers |
| `make restart` | Restart all containers (detached) |
| `make ps` | Show container status |
| `make logs` | Tail logs for all services |

### Dependencies

| Target | Description |
|---|---|
| `make deps-install` | Install both backend and frontend dependencies |
| `make deps-backend` | Install Composer dependencies |
| `make deps-frontend` | Install npm dependencies |

### Shell Access

| Target | Description |
|---|---|
| `make sh-backend` | Shell into backend container |
| `make sh-backend-root` | Shell into backend container as root |
| `make sh-frontend` | Shell into frontend container |
| `make sh-frontend-root` | Shell into frontend container as root |
| `make sh-db` | Shell into database container |

### Backend – Symfony

| Target | Description |
|---|---|
| `make console CMD="..."` | Run a Symfony console command |
| `make db-create` | Create the database if it doesn't exist |
| `make migrate` | Run Doctrine migrations |

### Frontend

| Target | Description |
|---|---|
| `make frontend-exec CMD="..."` | Run an arbitrary command in the frontend container |

---

## Docker Images

### Backend (`docker/backend/Dockerfile`)

- **Base:** `php:8.4-cli-bookworm`
- **PHP extensions:** `pdo_mysql`, `intl`, `zip`, `opcache`
- **Tooling:** Composer 2, Symfony CLI
- **User:** A non-root `app` user whose UID/GID matches the host (passed via build args)

### Frontend (`docker/frontend/Dockerfile`)

- **Base:** `node:24-bookworm-slim`
- **Runtime:** Node.js 24 with npm
- **User:** Runs as host UID/GID (set at compose level)

Both images use Debian Bookworm for consistency.

---

## File Ownership & Permissions

To prevent permission issues between the host and containers, the setup ensures:

1. **Backend:** The Dockerfile creates an `app` user with the host's UID/GID (passed as `HOST_UID`/`HOST_GID` build args). The container runs as this user.
2. **Frontend:** The container runs with `user: "${HOST_UID}:${HOST_GID}"` at the compose level (Node base image already includes a generic user mechanism).
3. **Makefile:** Automatically detects the host UID/GID via `id -u` / `id -g` and exports them for compose.

> If you change `HOST_UID`/`HOST_GID` in `docker/.env`, rebuild the backend image: `make build`.

---

## Common Workflows

### Adding a Composer package

```bash
make sh-backend
composer require some/package
```

### Adding an npm package

```bash
make sh-frontend
npm install some-package
```

### Running Symfony console commands

```bash
make console CMD="cache:clear"
make console CMD="make:controller HomeController"
```

### Rebuilding after Dockerfile changes

```bash
make build          # incremental
make build-no-cache # full rebuild
```

### Resetting the database

```bash
make down
docker volume rm notes_db_data
make up-d
make db-create
make migrate
```

---

## Troubleshooting

| Problem | Solution |
|---|---|
| Permission denied on `vendor/` or `node_modules/` | Verify `HOST_UID`/`HOST_GID` in `docker/.env` match your host user (`id -u && id -g`). Rebuild: `make build`. |
| Port already in use | Change the `*_PORT` variable in `docker/.env` and restart: `make restart`. |
| Database connection refused in Symfony | Ensure `DATABASE_URL` in `backend/.env.local` matches the `DB_*` values in `docker/.env`. The hostname is always `db` (the compose service name). |
| Containers won't start after pulling changes | Run `make build` to pick up Dockerfile changes, then `make up-d`. |
| Stale Symfony cache | `make console CMD="cache:clear"` |
