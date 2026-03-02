@echo off
REM Script to run contract expiration check
REM Location: c:\xamp\htdocs\sport_insightt\run-contract-expiration.bat

cd /d "C:\xamp\htdocs\sport_insightt"
C:\xampp\php\php.exe bin/console app:contract:expiration >> "C:\xampp\logs\contract-expiration.log" 2>&1
