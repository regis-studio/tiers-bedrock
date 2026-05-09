@echo off
rem
chcp 65001 >nul
setlocal enabledelayedexpansion

echo ===================================
echo   PMMP DevEnv Robust Refresher
echo ===================================
echo   Target : %~dp0
echo.

set "ERROR_COUNT=0"

echo [-] Cleaning old files...
for %%i in (vendor composer.json composer.lock) do (
    if exist "%%i" (
        if exist "%%i\*" (rd /s /q "%%i" || set /a ERROR_COUNT+=1) else (del /f /q "%%i" || set /a ERROR_COUNT+=1)
    )
)
if !ERROR_COUNT! gtr 0 (
    echo [!] Error: Cleanup failed.
    goto :FAILED
)

call :RUN_COMPOSER "pocketmine/pocketmine-mp" "--dev --ignore-platform-reqs" || goto :FAILED
call :RUN_COMPOSER "phpstan/phpstan" "--dev --ignore-platform-reqs" || goto :FAILED
call :RUN_COMPOSER "phpunit/phpunit" "--dev --ignore-platform-reqs" || goto :FAILED

echo.
echo ===================================
echo   Summary: SUCCESS
echo ===================================
goto :END

:RUN_COMPOSER
echo [+] Installing %~1...
call composer require %~2 %~1
if %errorlevel% neq 0 (
    echo [!] Failed to install %~1.
    exit /b 1
)
exit /b 0

:FAILED
echo.
echo ###################################
echo   ERROR: Process failed.
echo ###################################
pause
exit /b 1

:END
echo All tasks completed successfully.
pause