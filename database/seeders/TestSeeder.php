<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Team;

class TestSeeder extends Seeder { // Dev only
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $user = new User();
        $user->name = 'Fede';
        $user->role_id = 1;
        $user->email = 'fede@fede.fe';
        $user->password = '$2y$10$AXqenhA7PVXfwaWOgtIATeRNsOwfggJNVUlJ/oV86IgIk5KWY26/m';
        $user->save();

        $team = new Team();
        $team->name = 'Pizzaioli';
        $team->slug = 'pizzaioli';
        $team->url = 'http://dev.pizzareader.it/';
        $team->save();
    }
}
