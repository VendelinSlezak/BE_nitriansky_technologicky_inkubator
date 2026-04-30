<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AttachmentSeeder::class,
            ArticleSeeder::class,
            CompanySeeder::class,
            CompanyEmployeeSeeder::class,
            MentorSeeder::class,
            StudentSeeder::class,
            ProgramACategorySeeder::class,
            ChallengeSeeder::class,
            TeamSeeder::class,
            TeamMemberSeeder::class,
            CommissionSeeder::class,
            CommissionMemberSeeder::class,
            MilestoneSeeder::class,
        ]);
    }
}
