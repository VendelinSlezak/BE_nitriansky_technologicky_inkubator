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
                'id' => 1,
                'name' => 'Admin',
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
                'id' => 2,
                'name' => 'Student1',
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
                'id' => 3,
                'name' => 'Student2',
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
                'id' => 4,
                'name' => 'Student3',
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
                'id' => 5,
                'name' => 'Student4',
                'email' => 'nti4@bleskos.com',
                'password' => Hash::make('eee'),
                'role' => 'student',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 6,
                'name' => 'Mentor1',
                'email' => 'mentor1@bleskos.com',
                'password' => Hash::make('aaa'),
                'role' => 'mentor',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 7,
                'name' => 'Mentor2',
                'email' => 'mentor2@bleskos.com',
                'password' => Hash::make('aaa'),
                'role' => 'mentor',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 8,
                'name' => 'CommissionMember1',
                'email' => 'cm1@bleskos.com',
                'password' => Hash::make('aaa'),
                'role' => 'committee_member',
                'email_verified_at' => $now,
                'link_for_password_reset' => null,
                'expiration_of_link_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
