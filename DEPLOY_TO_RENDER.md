# Simple one-click deploy: Render.com (step by step)

This repository contains a `render.yaml` so Render can deploy the project automatically. Use the following steps to launch a working instance.

Minimum time: ~10 minutes

1. Sign in or create an account at https://dashboard.render.com
2. Connect your GitHub account and allow Render to access the repository for this project.
3. Click "New" → "Web Service" and choose this repository (branch: `main`). If Render detects `render.yaml` it will use that configuration.
4. Confirm the Build & Start settings. The repository already contains these commands:

   - buildCommand:
     - composer install --no-dev --optimize-autoloader
     - php artisan key:generate --force
     - php artisan migrate --force

   - startCommand: php artisan serve --host 0.0.0.0 --port 10000

5. Add required Environment Variables in Render dashboard under the service's Environment settings. Required keys to set (values are examples — DO NOT commit real secrets):

   - APP_ENV = production
   - APP_DEBUG = false
   - APP_URL = https://<your-app>.onrender.com
   - DB_CONNECTION = pgsql (or mysql if you choose a MySQL database)
   - DB_HOST = <render-db-host>
   - DB_PORT = 5432
   - DB_DATABASE = <database-name>
   - DB_USERNAME = <db-user>
   - DB_PASSWORD = <db-password>

6. Use Render's managed database or connect an existing one — `render.yaml` includes a Postgres database block for managed DBs.

7. Click Deploy and watch build logs. If builds fail because migrations cannot connect to the DB, double-check DB credentials and re-deploy (or run migrations using Render's shell console once DB is available).

8. After successful deploy, open your Render URL and verify the site loads.

Troubleshooting quick checks
- Migrations: run `php artisan migrate --force` from the Render Console
- Missing APP_KEY: run `php artisan key:generate --force` from Render Console
- Storage permission issues: check `storage/logs/laravel.log` in Render Console

If you'd like, I can also add a GitHub Action or more helpers — tell me which helper you want next.

### (Optional) One-click deploy from GitHub

I added a GitHub Actions workflow at `.github/workflows/deploy-to-render.yml` that triggers a Render deploy on every push to `main`.

To use it you need two repository secrets (in your GitHub repo settings → Settings → Secrets & variables → Actions):

- `RENDER_API_KEY` — your Render API key (create one at https://dashboard.render.com/account/api-keys)
- `RENDER_SERVICE_ID` — the service ID for the web service you created on Render (found in Render service URL or dashboard details)

After you add those secrets to the repo, any push to `main` will automatically POST to the Render API and create a new deploy.

If you want, I can walk you step-by-step inside your Render dashboard (watching your screen or you tell me what you see) and confirm everything is connected.
