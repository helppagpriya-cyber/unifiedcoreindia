# Unified Core India

Laravel application for the Unified Core India public website, CRM lead management, and EGJEWEL ERP administration.

## Requirements

- PHP 8.3 or later with the extensions required by Laravel and PhpSpreadsheet (including `fileinfo`, `mbstring`, `xml`, `zip`, and `gd`)
- Composer
- Node.js and npm
- MySQL/MariaDB for a local XAMPP installation, or another Laravel-supported database

## Local setup

1. Copy `.env.example` to `.env` and configure the application URL, database, and session/cache/queue settings for the local environment.
2. Install dependencies and generate an application key:

   ```bash
   composer install
   php artisan key:generate
   npm install
   ```

3. Create the configured database, then apply migrations and base CRM data:

   ```bash
   php artisan migrate --seed
   ```

4. Build frontend assets:

   ```bash
   npm run build
   ```

5. Configure an authenticated user for the relevant Filament panel. The EGJEWEL workspace is at `/egjewel`; the administration panel is at `/admin`.

For local development, `composer run dev` starts Laravel, the queue listener, logs, and Vite. In XAMPP, point the Apache document root at this project's `public` directory and make sure `mod_rewrite` and the required PHP extensions are enabled.

## Main areas

- `/` — public company website, service and industry information, and contact enquiry form.
- `/egjewel` — CRM and EGJEWEL software, client, and license administration.
- `/admin` — general project, client, user, role, and permission administration.

Public contact form submissions are validated, rate limited, and stored as unassigned CRM leads with the Website source and New status when those seeded values are available. CRM users can find them in the lead inbox and assign an owner.

The sales dashboard highlights unassigned enquiries and provides direct links to the matching filtered queues. Executives can bulk assign selected leads from the CRM inbox. Assignment changes are recorded in assignment history, and status changes are written to the lead activity timeline. A Lost status requires a reason so closed opportunities keep useful context.

## CRM lead import

The lead inbox supports Excel/CSV uploads. Validate a file, review individual rows in the Filament modal, correct invalid values, inspect duplicate matches, choose which rows to include, then confirm the import. Import preview files are kept on the private local disk and removed when the import is completed or discarded. The default upload size limit is 50 MB; PHP and Apache upload/post limits must also allow the chosen file size.

Seed data can be safely reapplied with:

```bash
php artisan db:seed
```

The CRM source and status seeder uses `updateOrCreate`, so the Website source and lead statuses remain available to the web form and import workflow.
