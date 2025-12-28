# RMA Graduation Project - Services Starter (PowerShell)

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "   RMA Graduation Project - Services Starter" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan

# Function to start a process in a new window
function Start-ServiceWindow {
    param($title, $command)
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "`$Host.UI.RawUI.WindowTitle = '$title'; $command"
}

Write-Host "[1/5] Starting Laravel Development Server..."
Start-ServiceWindow "Laravel Server" "php artisan serve"

Write-Host "[2/5] Starting Laravel Reverb (WebSockets)..."
Start-ServiceWindow "Laravel Reverb" "php artisan reverb:start --debug"

Write-Host "[3/5] Starting Laravel Queue Worker..."
Start-ServiceWindow "Laravel Queue" "php artisan queue:listen"

Write-Host "[4/5] Starting Laravel Scheduler (Local Runner)..."
Start-ServiceWindow "Laravel Scheduler" "php artisan schedule:work"

Write-Host "[5/5] Starting Vite Dev Server..."
Start-ServiceWindow "Vite Assets" "npm run dev"

Write-Host "`nAll services have been launched. Keep the windows open." -ForegroundColor Green
Write-Host "Press any key to exit this starter script..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
