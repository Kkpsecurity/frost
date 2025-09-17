const fs = require('fs');
const path = require('path');

console.log('📦 Starting asset update process...');

// Read the manifest file
const manifestPath = path.join(__dirname, "../public/build/manifest.json");
const dashboardPath = path.join(
    __dirname,
    "../resources/views/frontend/students/dashboard.blade.php"
);

try {
    // Check if manifest exists
    if (!fs.existsSync(manifestPath)) {
        console.error('❌ Manifest file not found at:', manifestPath);
        process.exit(1);
    }

    // Read manifest
    const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
    console.log('📦 Manifest loaded successfully');

    // Find the assets we need
    const studentEntry = manifest['resources/js/student.ts'];
    const routeUtilsAsset = Object.values(manifest).find(asset =>
        asset.file && asset.file.includes('routeUtils')
    );
    const bootstrapAsset = Object.values(manifest).find(asset =>
        asset.file && asset.file.includes('bootstrap')
    );

    if (!studentEntry) {
        console.error('❌ Student entry not found in manifest');
        process.exit(1);
    }

    console.log('📦 Found assets:');
    console.log('  - Student:', studentEntry.file);
    if (routeUtilsAsset) console.log('  - RouteUtils:', routeUtilsAsset.file);
    if (bootstrapAsset) console.log('  - Bootstrap:', bootstrapAsset.file);

    // Read the dashboard file
    if (!fs.existsSync(dashboardPath)) {
        console.error('❌ Dashboard file not found at:', dashboardPath);
        process.exit(1);
    }

    let dashboardContent = fs.readFileSync(dashboardPath, 'utf8');
    console.log('📄 Dashboard file loaded');

    // Replace @vite directive with individual script tags
    const viteRegex = /@vite\(\['resources\/js\/student\.ts'\]\)/g;

    // Only include script tags, no CSS link since student.ts doesn't generate CSS
    const newScriptTags = `<script src="/build/assets/bootstrap-fe102d55.js" type="module"></script>
        <script src="/build/assets/routeUtils-0c0a4e32.js" type="module"></script>
        <script src="/build/${studentEntry.file}" type="module"></script>`;

    if (dashboardContent.includes('@vite')) {
        dashboardContent = dashboardContent.replace(viteRegex, newScriptTags);

        // Write the updated content back
        fs.writeFileSync(dashboardPath, dashboardContent, 'utf8');
        console.log('✅ Successfully updated dashboard.blade.php with new asset references');
    } else {
        console.log('ℹ️ No @vite directives found to replace');
    }

} catch (error) {
    console.error('❌ Error updating assets:', error.message);
    process.exit(1);
}
