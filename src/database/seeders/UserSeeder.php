<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        DB::table('users')->insert([
            [
                'email' => 'nti@bleskos.com',
                'password' => Hash::make('aaa'),
                'role' => 'admin',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'email' => 'nti1@bleskos.com',
                'password' => Hash::make('bbb'),
                'role' => 'student',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'email' => 'nti2@bleskos.com',
                'password' => Hash::make('ccc'),
                'role' => 'student',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'email' => 'nti3@bleskos.com',
                'password' => Hash::make('ddd'),
                'role' => 'student',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'email' => 'nti4@bleskos.com',
                'password' => Hash::make('eee'),
                'role' => 'student',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
