Render deployment guide

1. Sign up and connect your repo

-   Create a Render account at https://render.com
-   Go to Dashboard → New → Web Service → Connect GitHub/GitLab and select this repository

2. Use the Render manifest or Dockerfile

-   This repo contains a `render.yaml` that describes a Docker web service and a `Dockerfile.render` that bundles nginx + php-fpm.
-   When creating the Web Service in the Render UI, select "Docker" as the environment and set the Dockerfile path to `Dockerfile.render` (Render will detect it if you use the manifest).

3. Set environment variables

-   Required minimal env vars:
    -   APP_ENV=production
    -   APP_DEBUG=false
    -   APP_KEY (generate locally and paste in Render dashboard or set as a secret)
    -   APP_URL=https://your-render-url.onrender.com
    -   DB_CONNECTION=postgres OR sqlite
    -   DATABASE_URL (if using managed Postgres on Render)
-   You can set these in the Render dashboard under the service Environment tab.

4. Add a managed Postgres (optional)

-   In the Render dashboard you can add a Postgres instance via "New → Database" and then attach it to your service.
-   Paste the provided DATABASE_URL into your service environment variables.

5. Deploy and run migrations

-   Render will auto-deploy when you push (if connected). Alternatively you can enable manual deploys.
-   Once the service is live, run migrations. You can use the deploy hooks or web shell in Render, or run the migration via the Render dashboard's Shell feature:
    -   php artisan migrate --force

6. Optional: GitHub Actions automated Hook (already included)

-   This repo includes `.github/workflows/render-deploy.yml` which triggers a deploy via Render API after a successful push to `main`/`master`.
-   To use it, add two GitHub secrets: `RENDER_SERVICE_ID` and `RENDER_API_KEY` (create an API key in Render Account Settings → API Keys; service ID is available in the Render dashboard or from the manifest).

7. Notes

-   For local development keep using `docker-compose.yml` (it uses separate nginx and db containers). `Dockerfile.render` is for Render's single-container environment.
-   Make sure `.env` is not committed. Use Render environment variables for secrets.
-   If you need queue workers, create a separate "Background Worker" service in Render and point it at the same repo; set the start command to run `php artisan queue:work`.

Need help setting secrets or running the first deploy? Tell me and I will prepare the exact commands and create a small helper script to run migrations via Render's shell.
