<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create(['nama_role' => 'Owner']);
        Role::create(['nama_role' => 'Gudang']);
        Role::create(['nama_role' => 'Kasir']);
            // Owner
        User::create([
            'name' => 'Owner User',
            'email' => 'owner@mail.com',
            'password' => Hash::make('12345'),
            'role_id' => 1, // asumsi id 1 = Owner
        ]);

        // Gudang
        User::create([
            'name' => 'Gudang User',
            'email' => 'gudang@mail.com',
            'password' => Hash::make('12345'),
            'role_id' => 2, // id 2 = Gudang
        ]);

        // Kasir
        User::create([
            'name' => 'Kasir User',
            'email' => 'kasir@mail.com',
            'password' => Hash::make('12345'),
            'role_id' => 3, // id 3 = Kasir
        ]);
    }
}
