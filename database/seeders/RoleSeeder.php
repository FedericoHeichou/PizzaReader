<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

class RoleSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        $role = new Role;
        $role->name = 'admin';
        $role->description = 'Can edit everything.';
        $role->save();

        $role = new Role;
        $role->name = 'manager';
        $role->description = 'Can edit other users and series.';
        $role->save();

        $role = new Role;
        $role->name = 'editor';
        $role->description = 'Can edit chapters of designated series.';
        $role->save();

        $role = new Role;
        $role->name = 'checker';
        $role->description = 'Can see chapters of designated series.';
        $role->save();

        $role = new Role;
        $role->name = 'user';
        $role->description = 'A regular user without privileges.';
        $role->save();


    }
}
