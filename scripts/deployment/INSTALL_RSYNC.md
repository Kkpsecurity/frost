# Installing rsync on Windows

To use rsync instead of robocopy for deployments, install rsync on your Windows system.

## Option 1: Using Chocolatey (Recommended)
```powershell
# Install Chocolatey if not already installed
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))

# Install rsync
choco install rsync
```

## Option 2: Using Windows Subsystem for Linux (WSL)
```powershell
# Enable WSL
wsl --install

# After WSL is installed and Ubuntu is set up:
wsl sudo apt update
wsl sudo apt install rsync
```

## Option 3: Using Git for Windows
If you have Git for Windows installed, rsync might already be available:
```powershell
# Check if available in Git Bash
"C:\Program Files\Git\usr\bin\rsync.exe" --version
```

## Option 4: Standalone rsync for Windows
Download from: https://www.itefix.net/cwrsync

## Verification
After installation, verify rsync is available:
```powershell
rsync --version
```

## Benefits of rsync over robocopy
- More efficient synchronization (only transfers changed files)
- Better handling of symbolic links
- More granular exclusion patterns
- Cross-platform compatibility
- Delta transfer algorithm (faster for large files with small changes)

---
*For immediate use, the deployment script will automatically fall back to robocopy if rsync is not available.*