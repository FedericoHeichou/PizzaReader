<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Valerie',
            'email' => 'valerie.levenets94@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
        $user->role()->associate(Role::where('name', 'admin')->first());
        $user->save();
    }
}
