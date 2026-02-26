# ==============================================================================
# FROST - Dev Server Sync Script
# Triggered by Husky post-commit hook
# Destination: \\develc\webroot\frost-devel
# ==============================================================================

$Source = "C:\laragon\www\frost"
$Dest = "\\develc\webroot\frost-devel"

Write-Host ""
Write-Host ">> Syncing to dev server: $Dest" -ForegroundColor Cyan

# Verify the destination is reachable
if (-not (Test-Path $Dest)) {
    Write-Host "!! Dev server not reachable: $Dest" -ForegroundColor Yellow
    Write-Host "   (Skipping sync - connect to the network and retry)" -ForegroundColor Yellow
    exit 0
}

# Run robocopy mirror sync
# /MIR  - mirror source to dest (adds + removes files)
# /XD   - exclude directories (preserve frost-devel-backup and other server-only dirs)
# /XF   - exclude files
# /NFL  - no file list       /NDL - no dir list
# /NJH  - no job header      /NJS - no job summary
# /NC   - no class labels    /NP  - no progress %
# /R:2  - retry 2 times on failure
# /W:3  - wait 3 seconds between retries
robocopy $Source $Dest /MIR `
    /XD ".git" "node_modules" "frost-devel-backup" "storage\app" "storage\logs" "storage\framework\cache" "storage\framework\sessions" "storage\framework\views" `
    /XF ".env" ".env.*" "*.log" `
    /NFL /NDL /NJH /NJS /NC /NP `
    /R:2 /W:3

$ExitCode = $LASTEXITCODE

# Robocopy exit codes 0-7 = success (8+ = errors)
if ($ExitCode -le 7) {
    Write-Host "   Sync complete (robocopy exit: $ExitCode)" -ForegroundColor Green
    exit 0
}
else {
    Write-Host "!! Sync FAILED (robocopy exit: $ExitCode)" -ForegroundColor Red
    exit 1
}
