@echo off
REM Quick Git Hooks Setup
REM Installs Git hooks for auto-deployment to staging

echo 🚀 Setting up Git hooks for auto-deployment...
echo.

powershell.exe -ExecutionPolicy Bypass -File "%~dp0setup-git-hooks.ps1" -Install

echo.
echo 📋 To test the setup, run:
echo    powershell -File "%~dp0setup-git-hooks.ps1" -Test
echo.
echo 🎯 To make a test commit that skips deployment:
echo    git commit -m "Test commit [skip deploy]"
echo.
pause
