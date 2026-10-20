# User Assessment System

Web application and token issuance infrastructure for a regional flight simulation community.

## Tech Stack
* PHP 8.2+
* Laravel
* Tailwind CSS / Vite

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database and Queues
php artisan migrate
php artisan queue:work

# Build assets
npm run build
```
