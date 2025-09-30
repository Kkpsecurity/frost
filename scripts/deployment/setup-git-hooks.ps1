# Git Hooks Setup Script
# Configures Git hooks for automatic deployment to staging

param(
    [switch]$Install = $false,
    [switch]$Uninstall = $false,
    [switch]$Test = $false,
    [switch]$Force = $false
)

$RepoRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$HooksDir = Join-Path $RepoRoot ".git\hooks"
$PostCommitHook = Join-Path $HooksDir "post-commit"
$PostCommitPS = Join-Path $HooksDir "post-commit.ps1"

function Write-Status {
    param([string]$Message, [string]$Color = "White")
    Write-Host $Message -ForegroundColor $Color
}

function Install-GitHooks {
    Write-Status "🔧 Installing Git hooks for auto-deployment..." "Blue"

    # Check if hooks already exist
    if ((Test-Path $PostCommitHook) -and -not $Force) {
        Write-Status "⚠️  Post-commit hook already exists. Use -Force to overwrite." "Yellow"
        return $false
    }

    try {
        # Create the bash version of post-commit hook
        $BashHookContent = @'
#!/bin/bash
# Git Post-Commit Hook - Auto Deploy to Staging
# Calls PowerShell script for actual deployment

REPO_ROOT=$(git rev-parse --show-toplevel)
PS_HOOK="$REPO_ROOT/.git/hooks/post-commit.ps1"

echo "🚀 Git Post-Commit: Starting auto-deployment..."

if command -v powershell.exe > /dev/null 2>&1; then
    powershell.exe -ExecutionPolicy Bypass -File "$PS_HOOK"
elif command -v pwsh > /dev/null 2>&1; then
    pwsh -ExecutionPolicy Bypass -File "$PS_HOOK"
else
    echo "❌ PowerShell not found. Please install PowerShell or PowerShell Core."
    exit 1
fi
'@

        Set-Content -Path $PostCommitHook -Value $BashHookContent -Encoding UTF8

        # Set permissions
        icacls $PostCommitHook /grant "Everyone:(RX)" /T 2>$null | Out-Null
        icacls $PostCommitPS /grant "Everyone:(RX)" /T 2>$null | Out-Null

        Write-Status "✅ Git hooks installed successfully!" "Green"
        Write-Status "📍 Hook location: $PostCommitHook" "Cyan"
        Write-Status "📍 PowerShell script: $PostCommitPS" "Cyan"

        Write-Status "`n📋 How it works:" "Blue"
        Write-Status "  • After each commit, the hook automatically deploys to \\atlas\webroot\frost-staging" "White"
        Write-Status "  • Add [skip deploy] to commit message to skip deployment" "White"
        Write-Status "  • Only deploys from main/master/copilot branches" "White"
        Write-Status "  • Creates backups before deployment" "White"
        Write-Status "  • Clears Laravel caches after deployment" "White"

        return $true
    }
    catch {
        Write-Status "❌ Failed to install Git hooks: $($_.Exception.Message)" "Red"
        return $false
    }
}

function Uninstall-GitHooks {
    Write-Status "🗑️  Uninstalling Git hooks..." "Yellow"

    $Removed = $false

    if (Test-Path $PostCommitHook) {
        Remove-Item $PostCommitHook -Force
        Write-Status "✅ Removed post-commit hook" "Green"
        $Removed = $true
    }

    if (Test-Path $PostCommitPS) {
        # Don't remove the PowerShell script, just notify
        Write-Status "ℹ️  PowerShell script preserved: $PostCommitPS" "Cyan"
    }

    if ($Removed) {
        Write-Status "✅ Git hooks uninstalled successfully!" "Green"
    }
    else {
        Write-Status "ℹ️  No Git hooks found to remove." "Yellow"
    }

    return $true
}

function Test-GitHooks {
    Write-Status "🧪 Testing Git hooks configuration..." "Blue"

    # Check if hooks exist
    $HookExists = Test-Path $PostCommitHook
    $PSScriptExists = Test-Path $PostCommitPS
    $StagingAccessible = Test-Path "\\atlas\webroot\frost-staging" -ErrorAction SilentlyContinue

    Write-Status "`n📋 Test Results:" "Blue"
    Write-Status "  Post-commit hook: $(if ($HookExists) { '✅ Exists' } else { '❌ Missing' })" $(if ($HookExists) { "Green" } else { "Red" })
    Write-Status "  PowerShell script: $(if ($PSScriptExists) { '✅ Exists' } else { '❌ Missing' })" $(if ($PSScriptExists) { "Green" } else { "Red" })
    Write-Status "  Staging server access: $(if ($StagingAccessible) { '✅ Accessible' } else { '❌ Not accessible' })" $(if ($StagingAccessible) { "Green" } else { "Red" })

    if ($HookExists -and $PSScriptExists) {
        Write-Status "`n🎯 Next steps:" "Blue"
        Write-Status "  1. Make a test commit to verify auto-deployment" "White"
        Write-Status "  2. Check deployment logs in deployment-staging.log" "White"
        Write-Status "  3. Verify files appear in \\atlas\webroot\frost-staging" "White"
        return $true
    }
    else {
        Write-Status "`n⚠️  Git hooks are not properly configured." "Yellow"
        Write-Status "     Run with -Install to set them up." "White"
        return $false
    }
}

function Show-Help {
    Write-Status "Git Hooks Setup Script" "Cyan"
    Write-Status "======================" "Cyan"
    Write-Status ""
    Write-Status "Configures Git hooks for automatic deployment to staging server" "White"
    Write-Status "Target: \\atlas\webroot\frost-staging" "Yellow"
    Write-Status ""
    Write-Status "Usage:" "Blue"
    Write-Status "  .\setup-git-hooks.ps1 -Install      # Install Git hooks" "White"
    Write-Status "  .\setup-git-hooks.ps1 -Uninstall    # Remove Git hooks" "White"
    Write-Status "  .\setup-git-hooks.ps1 -Test         # Test configuration" "White"
    Write-Status "  .\setup-git-hooks.ps1 -Install -Force  # Force reinstall" "White"
    Write-Status ""
    Write-Status "Features:" "Blue"
    Write-Status "  • Auto-deploy to staging after each commit" "White"
    Write-Status "  • Skip deployment with [skip deploy] in commit message" "White"
    Write-Status "  • Only deploys from main/master/copilot branches" "White"
    Write-Status "  • Automatic backups before deployment" "White"
    Write-Status "  • Post-deployment Laravel cache clearing" "White"
}

# Main execution
Write-Status "🚀 Git Hooks Setup for Frost Staging Deployment" "Cyan"
Write-Status "================================================" "Cyan"

if ($Install) {
    $Success = Install-GitHooks
    if ($Success) {
        Test-GitHooks | Out-Null
    }
}
elseif ($Uninstall) {
    Uninstall-GitHooks | Out-Null
}
elseif ($Test) {
    Test-GitHooks | Out-Null
}
else {
    Show-Help
}

Write-Status ""
