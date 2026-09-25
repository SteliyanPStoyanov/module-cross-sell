<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Create the "manage-cross-sell" staff permission used by the cross-sell admin resource.
 *
 * The Lunar 0.8 Admin Hub registered this permission in memory only; the Lunar 1.x
 * panel uses spatie/laravel-permission on the "staff" guard.
 */
return new class() extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate([
            'name' => 'manage-cross-sell',
            'guard_name' => 'staff',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
