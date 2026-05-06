<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = now();
        DB::table('files')->insert([
            [
                'id' => 1,
                'original_name' => 'document1.txt',
                'path' => 'documents/document1.txt',
                'disk' => 'public',
                'mime_type' => 'text/plain',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 2,
                'original_name' => 'document2.txt',
                'path' => 'documents/document2.txt',
                'disk' => 'public',
                'mime_type' => 'text/plain',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 3,
                'original_name' => 'document3.txt',
                'path' => 'documents/document3.txt',
                'disk' => 'public',
                'mime_type' => 'text/plain',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 4,
                'original_name' => 'logo-ukf.png',
                'path' => 'logos/logo-ukf.png',
                'disk' => 'public',
                'mime_type' => 'image/png',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'id' => 5,
                'original_name' => 'logo-ukf2.png',
                'path' => 'logos/logo-ukf2.png',
                'disk' => 'public',
                'mime_type' => 'image/png',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
        ]);
    }
}
