# OrbitSpace

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel)](https://laravel.com/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791?style=flat-square&logo=postgresql)](https://www.postgresql.org/)
[![stancl/tenancy](https://img.shields.io/badge/stancl%2Ftenancy-3.10-blueviolet?style=flat-square)](https://tenancyforlaravel.com/)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)

OrbitSpace is a multi-tenant blogging platform built with Laravel 13. Each blogger gets an isolated subdomain and a dedicated PostgreSQL database — no data leakage between tenants by design. The architecture is conceptually similar to early Blogspot: one central registration point, individual subdomains per blog.

---

## Table of Contents

- [Stack](#stack)
- [Architecture](#architecture)
- [Features](#features)
- [User Flow](#user-flow)
- [Project Structure](#project-structure)
- [Local Setup](#local-setup)
- [Environment Variables](#environment-variables)
- [Useful Commands](#useful-commands)
- [How Multi-Tenancy Works](#how-multi-tenancy-works)

---

## Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13.14 |
| Language | PHP 8.4 |
| Multi-tenancy | stancl/tenancy 3.10 |
| Database | PostgreSQL 16 |
| Authentication | Laravel Breeze (Blade) |
| Frontend | Tailwind CSS + Vite |
| Infrastructure | Docker (PostgreSQL container) |

---

## Architecture

OrbitSpace uses **multi-database tenancy**: every tenant (blogger) gets their own isolated PostgreSQL database. The central application database stores only tenant metadata.

```
central.test                      slug.central.test
-----------                       -----------------
central DB (orbitspace)           tenant DB (tenant_slug)
  tenants                           users
  domains                           posts
                                    blog_settings
                                    cache
                                    jobs
```

Tenant identification is done by **subdomain**: the tenancy middleware reads the host header, looks up the matching domain record in the central database, and switches the database connection for the duration of that request.

---

## Features

- **Blogger registration** — registers on the central domain, tenant and dedicated database are created automatically
- **Subdomain routing** — each blogger accesses their panel at `slug.central.test`
- **Post management** — full CRUD with title, content, cover image, and draft/published status
- **Blog customisation** — blog name, bio, avatar, background color, text color, accent color, and font family
- **Super Admin panel** — lists all tenants, suspend and reactivate accounts
- **Automatic post-registration redirect** — after sign-up the user is sent directly to their tenant subdomain

---

## User Flow

```
central.test/register
  └── blogger fills name, email, password, blog slug
  └── tenant + database created
  └── redirect to slug.central.test/posts

slug.central.test/login
  └── authenticates against tenant database
  └── redirect to /posts

slug.central.test/posts
  └── create, edit, delete posts

slug.central.test/blog-settings
  └── customise blog appearance

central.test/admin
  └── super admin: list all tenants, suspend / reactivate
```

---

## Project Structure

Only the directories relevant to the multi-tenant architecture are shown below.

```
orbitspace/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/
│   │       │   └── AdminController.php           # Tenant list, suspend, reactivate
│   │       ├── Auth/
│   │       │   ├── RegisteredUserController.php  # Creates tenant + DB on register
│   │       │   └── AuthenticatedSessionController.php
│   │       ├── PostController.php
│   │       └── BlogSettingController.php
│   └── Models/
│       ├── Tenant.php                            # Extends BaseTenant (stancl)
│       ├── Post.php
│       └── BlogSetting.php
├── database/
│   ├── migrations/                               # Central DB migrations
│   │   ├── ..._create_tenants_table.php
│   │   └── ..._create_domains_table.php
│   └── migrations/tenant/                        # Run per-tenant via artisan
│       ├── ..._create_users_table.php
│       ├── ..._create_posts_table.php
│       ├── ..._create_blog_settings_table.php
│       ├── ..._create_cache_table.php
│       └── ..._create_jobs_table.php
├── routes/
│   ├── web.php                                   # Central domain routes
│   ├── tenant.php                                # Tenant subdomain routes
│   └── auth.php                                  # Shared auth routes (required in both)
└── config/
    └── tenancy.php                               # Tenancy configuration
```

---

## Local Setup

### Prerequisites

- PHP 8.4 with extensions: `pgsql`, `pdo_pgsql`, `mbstring`, `xml`, `curl`
- Composer
- Node.js 20+
- Docker (for PostgreSQL)

### 1. Clone and install dependencies

```bash
git clone https://github.com/your-username/orbitspace.git
cd orbitspace
composer install
npm install
```

### 2. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with the values from the [Environment Variables](#environment-variables) section.

### 3. Start the PostgreSQL container

```bash
docker run -d \
  --name orbitspace-postgres \
  -e POSTGRES_USER=orbitspace \
  -e POSTGRES_PASSWORD=secret \
  -e POSTGRES_DB=orbitspace \
  -p 5433:5432 \
  postgres:16
```

### 4. Run central migrations

```bash
php artisan migrate
```

This creates the `tenants` and `domains` tables in the central database.

### 5. Configure local subdomain resolution

Add the following entries to your system hosts file.

**Linux / macOS:** `/etc/hosts`
**Windows:** `C:\Windows\System32\drivers\etc\hosts`

```
127.0.0.1  central.test
127.0.0.1  juan.central.test
127.0.0.1  ana.central.test
```

Add a new line for each tenant slug you create during development. There is no wildcard DNS resolver in this setup — each subdomain must be declared manually.

### 6. Build frontend assets

```bash
npm run build
# or for development with hot reload:
npm run dev
```

### 7. Start the development server

```bash
php artisan serve --host=central.test --port=8000
```

The application is now accessible at `http://central.test:8000`.

---

## Environment Variables

The following variables must be set in `.env` for the application to function correctly. The defaults from `.env.example` are for SQLite — replace them entirely.

```dotenv
APP_NAME=OrbitSpace
APP_ENV=local
APP_KEY=                          # generated by php artisan key:generate
APP_DEBUG=true
APP_URL=http://central.test:8000

# The central domain. Tenancy uses this to distinguish central from tenant requests.
CENTRAL_DOMAIN=central.test

# Central PostgreSQL database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=orbitspace
DB_USERNAME=orbitspace
DB_PASSWORD=secret

# Tenant databases are created dynamically by stancl/tenancy.
# They use the same host, port, and credentials as the central DB.

SESSION_DRIVER=database
SESSION_DOMAIN=.central.test     # Leading dot allows subdomain session sharing

QUEUE_CONNECTION=database
CACHE_STORE=database
```

> **SESSION_DOMAIN**: the leading dot (`.central.test`) is required. Without it, the session cookie set on `central.test` will not be sent on requests to `slug.central.test`, causing authentication to fail after cross-domain redirects.

---

## Useful Commands

```bash
# Run tenant migrations on all existing tenant databases
php artisan tenants:migrate

# Run tenant migrations for a specific tenant
php artisan tenants:migrate --tenants=juan

# Roll back tenant migrations on all tenant databases
php artisan tenants:migrate:rollback

# Open a Tinker session in the context of a specific tenant
php artisan tinker
# then inside tinker: tenancy()->initialize(Tenant::find('juan'))

# List all registered tenants
php artisan tinker --execute="Tenant::all(['id'])->pluck('id')"

# Run all queued jobs (tenant jobs run within the correct tenant context)
php artisan queue:work

# Compile assets for production
npm run build

# Run the full dev environment (server + queue + logs + vite)
composer dev
```

---

## How Multi-Tenancy Works

### Request lifecycle

Every incoming HTTP request passes through one of two route groups:

**Central routes** (`routes/web.php`) — scoped to `central.test` via `Route::domain()`. Handles registration, the admin panel, and the welcome page. No tenancy context is initialized here.

**Tenant routes** (`routes/tenant.php`) — uses the `InitializeTenancyByDomain` and `PreventAccessFromCentralDomains` middleware. When a request arrives at `juan.central.test`, the middleware queries the `domains` table for a matching record, retrieves the associated tenant, and switches the active database connection to that tenant's dedicated database. All subsequent queries within the request hit the tenant database exclusively.

### Tenant creation

When a blogger registers, `RegisteredUserController::store()` executes the following sequence:

1. Creates a record in the `tenants` table with the chosen slug as the primary key.
2. Creates a record in the `domains` table linking the full subdomain (`slug.central.test`) to the tenant.
3. Calls `tenancy()->initialize($tenant)`, which creates the tenant's PostgreSQL database and switches the active connection.
4. Creates the user record inside the tenant's database and authenticates them.
5. Redirects to `http://slug.central.test:8000/posts`.

### Data isolation

Each tenant database is entirely separate. A query to the `posts` table on `juan.central.test` cannot reach the posts of `ana.central.test` — they live in different databases. The only shared state is in the central database (`tenants`, `domains`).

### VirtualColumn and the `data` column

`BaseTenant` uses the `VirtualColumn` trait (via `HasDataColumn`) to store arbitrary tenant attributes in a JSON column named `data`. The trait manages a two-phase encode/decode cycle:

- **After retrieval**: the JSON stored in `data` is unpacked into flat Eloquent model attributes; the `data` attribute itself is set to `null`.
- **Before saving**: flat attributes that do not correspond to real database columns are repacked into the `data` JSON column.

As a result, custom tenant attributes such as `suspended` must be set and read as **flat model attributes**, not as keys inside `$tenant->data`:

```php
// Correct — VirtualColumn repacks this into the JSON data column on save
$tenant->suspended = true;
$tenant->save();

// Reading — plain attribute access after retrieval
$tenant->suspended; // true

// Incorrect — $tenant->data is null after VirtualColumn decodes it; always returns null
$tenant->data['suspended'];
```


