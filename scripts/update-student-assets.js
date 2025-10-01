#!/usr/bin/env node

/**
 * Update Student Assets Script
 *
 * This script runs after Vite build to update student-specific assets
 * and ensure proper asset management for the student dashboard and components.
 */

const fs = require('fs');
const path = require('path');

console.log('🎯 Starting student assets update...');

// Define paths
const publicBuildPath = path.join(__dirname, '..', 'public', 'build');
const manifestPath = path.join(publicBuildPath, 'manifest.json');
const studentAssetsPath = path.join(__dirname, '..', 'public', 'assets', 'student');

// Ensure student assets directory exists
if (!fs.existsSync(studentAssetsPath)) {
    fs.mkdirSync(studentAssetsPath, { recursive: true });
    console.log('📁 Created student assets directory');
}

// Read the Vite manifest
let manifest = {};
try {
    if (fs.existsSync(manifestPath)) {
        const manifestContent = fs.readFileSync(manifestPath, 'utf8');
        manifest = JSON.parse(manifestContent);
        console.log('✅ Loaded Vite manifest');
    } else {
        console.log('⚠️  Manifest not found, creating empty one');
    }
} catch (error) {
    console.error('❌ Error reading manifest:', error.message);
    process.exit(1);
}

// Create a student-specific asset manifest
const studentManifest = {
    generated_at: new Date().toISOString(),
    build_info: {
        vite_version: '4.5.14',
        environment: process.env.NODE_ENV || 'production',
        timestamp: Date.now()
    },
    assets: {
        // Extract student-related assets from main manifest
        student: {},
        dashboard: {},
        components: {}
    }
};

// Process manifest entries for student-related assets
Object.keys(manifest).forEach(key => {
    const entry = manifest[key];

    // Check if this is a student-related asset
    if (key.includes('student') || key.includes('Student')) {
        studentManifest.assets.student[key] = entry;
    }

    // Check if this is a dashboard-related asset
    if (key.includes('dashboard') || key.includes('Dashboard')) {
        studentManifest.assets.dashboard[key] = entry;
    }

    // Check if this is a component-related asset
    if (key.includes('components') || key.includes('Components')) {
        studentManifest.assets.components[key] = entry;
    }
});

// Write student manifest
const studentManifestPath = path.join(studentAssetsPath, 'manifest.json');
try {
    fs.writeFileSync(studentManifestPath, JSON.stringify(studentManifest, null, 2));
    console.log('✅ Created student asset manifest');
} catch (error) {
    console.error('❌ Error writing student manifest:', error.message);
}

// Create asset summary for debugging
const summaryPath = path.join(studentAssetsPath, 'build-summary.json');
const summary = {
    build_time: new Date().toISOString(),
    total_assets: Object.keys(manifest).length,
    student_assets: Object.keys(studentManifest.assets.student).length,
    dashboard_assets: Object.keys(studentManifest.assets.dashboard).length,
    component_assets: Object.keys(studentManifest.assets.components).length,
    build_size: calculateBuildSize(publicBuildPath),
    status: 'completed'
};

try {
    fs.writeFileSync(summaryPath, JSON.stringify(summary, null, 2));
    console.log('✅ Created build summary');
} catch (error) {
    console.error('❌ Error writing build summary:', error.message);
}

// Helper function to calculate build size
function calculateBuildSize(buildPath) {
    let totalSize = 0;

    try {
        const files = fs.readdirSync(buildPath, { recursive: true });

        files.forEach(file => {
            const filePath = path.join(buildPath, file);
            try {
                const stats = fs.statSync(filePath);
                if (stats.isFile()) {
                    totalSize += stats.size;
                }
            } catch (error) {
                // Skip files that can't be accessed
            }
        });
    } catch (error) {
        console.warn('⚠️  Could not calculate build size:', error.message);
    }

    return {
        bytes: totalSize,
        kb: Math.round(totalSize / 1024),
        mb: Math.round(totalSize / (1024 * 1024) * 100) / 100
    };
}

// Log completion
console.log('🎉 Student assets update completed!');
console.log(`📊 Summary:`);
console.log(`   - Total assets: ${Object.keys(manifest).length}`);
console.log(`   - Student assets: ${Object.keys(studentManifest.assets.student).length}`);
console.log(`   - Dashboard assets: ${Object.keys(studentManifest.assets.dashboard).length}`);
console.log(`   - Component assets: ${Object.keys(studentManifest.assets.components).length}`);
console.log(`   - Build size: ${summary.build_size.mb} MB`);
console.log('');

process.exit(0);
