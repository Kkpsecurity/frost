# PowerShell script to create SSL certificates for Vite development server
# This script will create self-signed certificates for frost.test

$certDir = "certs"
$domain = "frost.test"

Write-Host "Creating SSL certificates for $domain..." -ForegroundColor Green

# Check if mkcert is available
$mkcertAvailable = Get-Command mkcert -ErrorAction SilentlyContinue

if ($mkcertAvailable) {
    Write-Host "Using mkcert to create certificates..." -ForegroundColor Yellow

    # Create certificates using mkcert
    mkcert -key-file "$certDir/$domain-key.pem" -cert-file "$certDir/$domain+1.pem" $domain "localhost"

    # Rename the cert file to match vite.config.js expectations
    if (Test-Path "$certDir/$domain+1.pem") {
        Move-Item "$certDir/$domain+1.pem" "$certDir/$domain.pem"
    }

    Write-Host "Certificates created successfully with mkcert!" -ForegroundColor Green
} else {
    Write-Host "mkcert not found. Creating self-signed certificates with OpenSSL..." -ForegroundColor Yellow

    # Check if OpenSSL is available
    $opensslAvailable = Get-Command openssl -ErrorAction SilentlyContinue

    if ($opensslAvailable) {
        # Create self-signed certificate with OpenSSL
        openssl req -x509 -newkey rsa:4096 -keyout "$certDir/$domain-key.pem" -out "$certDir/$domain.pem" -days 365 -nodes -subj "/C=US/ST=Local/L=Local/O=Development/CN=$domain"

        Write-Host "Self-signed certificates created with OpenSSL!" -ForegroundColor Green
    } else {
        Write-Host "Neither mkcert nor OpenSSL found." -ForegroundColor Red
        Write-Host "Please install mkcert or OpenSSL to generate certificates." -ForegroundColor Red
        Write-Host ""
        Write-Host "Alternative: Install mkcert:" -ForegroundColor Yellow
        Write-Host "  winget install FiloSottile.mkcert" -ForegroundColor White
        Write-Host "  or download from: https://github.com/FiloSottile/mkcert/releases" -ForegroundColor White
        exit 1
    }
}

Write-Host ""
Write-Host "Certificates created in $certDir/ directory:" -ForegroundColor Green
Write-Host "  - $domain-key.pem (private key)" -ForegroundColor White
Write-Host "  - $domain.pem (certificate)" -ForegroundColor White
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Restart your Vite development server: npm run dev" -ForegroundColor White
Write-Host "2. Vite should now run on https://frost.test:5173" -ForegroundColor White
