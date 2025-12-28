@echo off
TITLE RMA Project Services Manager
COLOR 0A

echo ====================================================
echo    RMA Graduation Project - Services Starter
echo ====================================================
echo.
echo [1/5] Starting Laravel Development Server...
start "Laravel Server (Port 8000)" cmd /k "php artisan serve"

echo [2/5] Starting Laravel Reverb (WebSockets)...
start "Laravel Reverb" cmd /k "php artisan reverb:start --debug"

echo [3/5] Starting Laravel Queue Worker...
start "Laravel Queue" cmd /k "php artisan queue:listen"

echo [4/5] Starting Laravel Scheduler (Local Runner)...
start "Laravel Scheduler" cmd /k "php artisan schedule:work"

echo [5/5] Starting Vite Dev Server...
start "Vite Assets" cmd /k "npm run dev"

echo.
echo ====================================================
echo  All services have been launched in separate windows.
echo  Please keep these windows open while developing.
echo ====================================================
echo.
pause