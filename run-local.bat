@echo off
echo ============================================
echo Building and starting Docker Compose services
echo ============================================

docker-compose up -d --build

echo.
echo Waiting a few seconds for services to start...
timeout /t 5 /nobreak >nul

echo Running database migrations in the app container...
docker exec -i laravel_app php artisan migrate --force

echo.
echo Application should be available at http://localhost:10000
echo Press any key to exit.
pause >nul
