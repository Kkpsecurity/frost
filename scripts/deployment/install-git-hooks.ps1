param([switch]$Install, [switch]$Test)

$RepoRoot = git rev-parse --show-toplevel
$PostCommitHook = Join-Path $RepoRoot ".git\hooks\post-commit"

Write-Host "🚀 Git Hooks Setup" -ForegroundColor Cyan

if ($Install) {
    Write-Host "Installing Git hook..." -ForegroundColor Blue

    $HookContent = '#!/bin/bash
echo "🚀 Auto-deploying to staging..."
powershell.exe -ExecutionPolicy Bypass -File "$(git rev-parse --show-toplevel)/.git/hooks/post-commit.ps1"'

    Set-Content -Path $PostCommitHook -Value $HookContent
    Write-Host "✅ Git hook installed!" -ForegroundColor Green
}
elseif ($Test) {
    $HookExists = Test-Path $PostCommitHook
    Write-Host "Hook exists: $HookExists" -ForegroundColor $(if ($HookExists) { "Green" } else { "Red" })
}
else {
    Write-Host "Usage: -Install or -Test"
}
