<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EnsurePeopleViewPermissionOnRoles extends Migration
{
    public function up()
    {
        // Ensure people.view exists in permissions table
        $permId = DB::table('permissions')->where('name', 'people.view')->value('id');
        if (!$permId) {
            $permId = DB::table('permissions')->insertGetId([
                'name'         => 'people.view',
                'display_name' => 'View People',
                'description'  => 'Allow viewing actor pages on the site.',
                'group'        => 'people',
            ]);
        }

        // Attach to guests role and default (users) role via polymorphic permissionables table
        $roleClass = 'Common\Auth\Roles\Role';
        $roleIds = DB::table('roles')
            ->where('guests', 1)
            ->orWhere('default', 1)
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('permissionables')
                ->where('permission_id', $permId)
                ->where('permissionable_id', $roleId)
                ->where('permissionable_type', $roleClass)
                ->exists();
            if (!$exists) {
                DB::table('permissionables')->insert([
                    'permission_id'      => $permId,
                    'permissionable_id'  => $roleId,
                    'permissionable_type' => $roleClass,
                    'restrictions'       => null,
                ]);
            }
        }
    }

    public function down()
    {
        // intentionally left empty
    }
}
