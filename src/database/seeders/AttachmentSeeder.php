<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('Najprv spusti UserSeeder, aby mal k čomu priradiť prílohy.');
            return;
        }

        foreach ($users as $user) {
            Attachment::factory()
                ->count(rand(1, 3))
                ->create([
                    'attachable_id' => $user->id,
                    'attachable_type' => get_class($user),
                ]);
        }
    }
}