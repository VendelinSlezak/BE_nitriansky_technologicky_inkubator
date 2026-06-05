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
                'email' => 'nti@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'admin',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2,
                'name' => 'Student1',
                'email' => 's1@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'student',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3,
                'name' => 'Student2',
                'email' => 's2@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'student',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 4,
                'name' => 'Student3',
                'email' => 's3@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'student',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 5,
                'name' => 'CommissionMember2',
                'email' => 'cm2@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'committee_member',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 6,
                'name' => 'Mentor1',
                'email' => 'mentor1@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'mentor',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 7,
                'name' => 'Mentor2',
                'email' => 'mentor2@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'mentor',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 8,
                'name' => 'CommissionMember1',
                'email' => 'cm1@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'committee_member',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 9,
                'name' => 'WebEditor1',
                'email' => 'we1@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'web_editor',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 10,
                'name' => 'Company1',
                'email' => 'company1@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'company_admin',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 11,
                'name' => 'CompanyMember1',
                'email' => 'po1@nti.com',
                'password' => Hash::make('aaa'),
                'role' => 'company_member',
                'email_verified_at' => $now,
                'token_for_password_reset' => null,
                'expiration_of_token_for_password_reset' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
