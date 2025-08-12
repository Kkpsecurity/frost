<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PermissionIntegrationService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ManagePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:manage
                            {action : The action to perform (list, sync, create, assign)}
                            {--role= : Role name for assign action}
                            {--permission= : Permission name for assign action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage permissions and sync users with roles';

    private PermissionIntegrationService $permissionService;

    public function __construct(PermissionIntegrationService $permissionService)
    {
        parent::__construct();
        $this->permissionService = $permissionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->listPermissions();
                break;

            case 'sync':
                $this->syncUsers();
                break;

            case 'create':
                $this->createPermission();
                break;

            case 'assign':
                $this->assignPermission();
                break;

            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: list, sync, create, assign');
        }
    }

    private function listPermissions()
    {
        $this->info('=== Spatie Roles ===');
        Role::all()->each(function ($role) {
            $permissions = $role->permissions->pluck('name')->join(', ');
            $this->line("{$role->name}: {$permissions}");
        });

        $this->info("\n=== All Permissions ===");
        Permission::all()->each(function ($permission) {
            $this->line("- {$permission->name}");
        });
    }

    private function syncUsers()
    {
        $this->info('Syncing all users with their Spatie roles...');
        $this->permissionService->syncAllUsers();
        $this->info('✓ All users synced successfully');
    }

    private function createPermission()
    {
        $permission = $this->ask('Enter permission name:');

        if (!$permission) {
            $this->error('Permission name is required');
            return;
        }

        $created = $this->permissionService->createPermission($permission);
        $this->info("✓ Permission '{$created->name}' created successfully");
    }

    private function assignPermission()
    {
        $role = $this->option('role') ?? $this->ask('Enter role name:');
        $permission = $this->option('permission') ?? $this->ask('Enter permission name:');

        if (!$role || !$permission) {
            $this->error('Both role and permission are required');
            return;
        }

        $success = $this->permissionService->assignPermissionToRole($role, $permission);

        if ($success) {
            $this->info("✓ Permission '{$permission}' assigned to role '{$role}'");
        } else {
            $this->error("✗ Failed to assign permission. Check if role and permission exist.");
        }
    }
}
