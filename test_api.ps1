Write-Host "=== TESTING ACTUAL API ENDPOINT ===" -ForegroundColor Cyan
Write-Host ""

$response = Invoke-WebRequest -Uri "https://frost.test/classroom/class/data?course_auth_id=2" `
    -Headers @{
        "Cookie" = (Get-Content ".\test_cookie.txt" -ErrorAction SilentlyContinue)
    } `
    -UseBasicParsing `
    -ErrorAction Stop

$json = $response.Content | ConvertFrom-Json

Write-Host "Response Status: $($response.StatusCode)" -ForegroundColor Green
Write-Host ""
Write-Host "StudentUnit data:" -ForegroundColor Yellow
$json.data.studentUnit | ConvertTo-Json -Depth 5
Write-Host ""

if ($json.data.studentUnit.onboarding_completed -eq $true) {
    Write-Host "✅ onboarding_completed = TRUE" -ForegroundColor Green
    Write-Host "✅ Student should be allowed into classroom" -ForegroundColor Green
} else {
    Write-Host "❌ onboarding_completed = FALSE" -ForegroundColor Red
    Write-Host "❌ Student will see onboarding screen" -ForegroundColor Red
}
