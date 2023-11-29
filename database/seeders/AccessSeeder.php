<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => "1",
            'name' => "Admin",
            'guard_name' => "backpack",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

        ]);
        DB::table('permissions')->insert([
            'id' => "1",
            'name' => "User Management",
            'guard_name' => "backpack",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => "1",
            'model_type' => "App\Models\User",
            'model_id' => "1",


        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => "1",
            'model_type' => "App\Models\User",
            'model_id' => "2",


        ]);
        DB::table('role_has_permissions')->insert([
            'permission_id' => "1",
            'role_id' => "1",
        ]);

    }
}
