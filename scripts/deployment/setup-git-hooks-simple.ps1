# Git Hooks Setup Script - Simplified Version
param([switch]$Install, [switch]$Uninstall, [switch]$Test, [switch]$Force)

$RepoRoot = git rev-parse --show-toplevel
$HooksDir = Join-Path $RepoRoot ".git\hooks"
$PostCommitHook = Join-Path $HooksDir "post-commit"

Write-Host "🚀 Git Hooks Setup for Frost Staging Deployment" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan

if ($Install) {
    Write-Host "🔧 Installing Git hooks..." -ForegroundColor Blue

    if ((Test-Path $PostCommitHook) -and -not $Force) {
        Write-Host "⚠️  Post-commit hook already exists. Use -Force to overwrite." -ForegroundColor Yellow
        exit 1
    }

    $HookContent = @'
#!/bin/bash
echo "🚀 Auto-deploying to staging..."
powershell.exe -ExecutionPolicy Bypass -File "$(git rev-parse --show-toplevel)/.git/hooks/post-commit.ps1"
'@

    Set-Content -Path $PostCommitHook -Value $HookContent -Encoding UTF8
    Write-Host "✅ Git hook installed successfully!" -ForegroundColor Green
    Write-Host "📍 Hook location: $PostCommitHook" -ForegroundColor Cyan
} elseif ($Uninstall) {
    Write-Host "🗑️  Uninstalling Git hooks..." -ForegroundColor Yellow

    if (Test-Path $PostCommitHook) {
        Remove-Item $PostCommitHook -Force
        Write-Host "✅ Git hook removed successfully!" -ForegroundColor Green
    } else {
        Write-Host "ℹ️  No Git hook found to remove." -ForegroundColor Yellow
    }
} elseif ($Test) {
    Write-Host "🧪 Testing Git hooks configuration..." -ForegroundColor Blue

    $HookExists = Test-Path $PostCommitHook
    $PSScriptExists = Test-Path (Join-Path $HooksDir "post-commit.ps1")
    $StagingAccessible = Test-Path "\\atlas\webroot\frost-staging" -ErrorAction SilentlyContinue

    Write-Host "`n📋 Test Results:" -ForegroundColor Blue
    Write-Host "  Post-commit hook: $(if ($HookExists) { '✅ Exists' } else { '❌ Missing' })" -ForegroundColor $(if ($HookExists) { "Green" } else { "Red" })
    Write-Host "  PowerShell script: $(if ($PSScriptExists) { '✅ Exists' } else { '❌ Missing' })" -ForegroundColor $(if ($PSScriptExists) { "Green" } else { "Red" })
    Write-Host "  Staging server: $(if ($StagingAccessible) { '✅ Accessible' } else { '❌ Not accessible' })" -ForegroundColor $(if ($StagingAccessible) { "Green" } else { "Red" })

    if ($HookExists -and $PSScriptExists -and $StagingAccessible) {
        Write-Host "`n🎉 Configuration looks good! Make a commit to test auto-deployment." -ForegroundColor Green
    } else {
        Write-Host "`n⚠️  Configuration needs attention. Run -Install to set up." -ForegroundColor Yellow
    }
} else {
    Write-Host "Usage:" -ForegroundColor Blue
    Write-Host "  .\setup-git-hooks-simple.ps1 -Install      # Install Git hooks" -ForegroundColor White
    Write-Host "  .\setup-git-hooks-simple.ps1 -Uninstall    # Remove Git hooks" -ForegroundColor White
    Write-Host "  .\setup-git-hooks-simple.ps1 -Test         # Test configuration" -ForegroundColor White
    Write-Host "  .\setup-git-hooks-simple.ps1 -Install -Force  # Force reinstall" -ForegroundColor White
    Write-Host ""
    Write-Host "Auto-deployment target: \\atlas\webroot\frost-staging" -ForegroundColor Yellow
    Write-Host "Skip deployment with: [skip deploy] in commit message" -ForegroundColor Yellow
}
