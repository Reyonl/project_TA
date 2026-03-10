<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama_admin' => 'Administrator',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
            ]
        );

        Admin::updateOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'nama_admin' => 'Owner Toko',
                'password'   => Hash::make('password'),
                'role'       => 'owner',
            ]
        );
    }
}
