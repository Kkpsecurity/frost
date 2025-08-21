<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\MediaManager;

class InitializeMediaStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:init {--force : Force recreation of directories}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the media directory structure based on config/media.php';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing media directory structure...');
        
        try {
            MediaManager::ensureDirectoryStructure();
            
            $this->info('✅ Media directory structure created successfully!');
            
            // Display the structure
            $this->displayStructure();
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to initialize media structure: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Display the created directory structure
     */
    protected function displayStructure()
    {
        $this->newLine();
        $this->info('📁 Created directory structure:');
        $this->newLine();
        
        $categories = MediaManager::getCategories();
        
        foreach ($categories as $categoryName => $categoryConfig) {
            $this->line("📂 <fg=yellow>{$categoryConfig['directory']}/</> ({$categoryConfig['description']})");
            
            foreach ($categoryConfig['subdirectories'] as $subName => $subConfig) {
                $this->line("  └── <fg=cyan>{$subConfig['path']}/</>");
                
                if (isset($subConfig['subdirs'])) {
                    foreach ($subConfig['subdirs'] as $nestedDir => $description) {
                        $this->line("      └── <fg=green>{$nestedDir}/</> - {$description}");
                    }
                }
            }
            $this->newLine();
        }
        
        $this->info('🎯 Media structure ready for use!');
        $this->info('💡 Use MediaManager::storeAvatar(), MediaManager::courseContent(), etc. for file operations');
    }
}
