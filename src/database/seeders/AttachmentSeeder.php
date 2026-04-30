<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $date = now();
        DB::table('attachments')->insert([
            [
                'public_id' => 1,
                'attachable_id' => 1,
                'attachable_type' => 'App\Models\Challenge',
                'collection' => 'attachment',
                'visibility' => 'public',
                'disk' => 'public',
                'path' => 'documents/document1.txt',
                'original_name' => 'document1.txt',
                'stored_name' => 'document1.txt',
                'mime_type' => 'text/plain',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'public_id' => 2,
                'attachable_id' => 2,
                'attachable_type' => 'App\Models\Challenge',
                'collection' => 'attachment',
                'visibility' => 'public',
                'disk' => 'public',
                'path' => 'documents/document2.txt',
                'original_name' => 'document2.txt',
                'stored_name' => 'document2.txt',
                'mime_type' => 'text/plain',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'public_id' => 3,
                'attachable_id' => 3,
                'attachable_type' => 'App\Models\Challenge',
                'collection' => 'attachment',
                'visibility' => 'public',
                'disk' => 'public',
                'path' => 'documents/document3.txt',
                'original_name' => 'document3.txt',
                'stored_name' => 'document3.txt',
                'mime_type' => 'text/plain',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'public_id' => 4,
                'attachable_id' => 4,
                'attachable_type' => 'App\Models\Company',
                'collection' => 'attachment',
                'visibility' => 'public',
                'disk' => 'public',
                'path' => 'logos/logo-ukf.png',
                'original_name' => 'logo-ukf.png',
                'stored_name' => 'logo-ukf.png',
                'mime_type' => 'image/png',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'public_id' => 5,
                'attachable_id' => 5,
                'attachable_type' => 'App\Models\Article',
                'collection' => 'attachment',
                'visibility' => 'public',
                'disk' => 'public',
                'path' => 'logos/logo-ukf2.png',
                'original_name' => 'logo-ukf2.png',
                'stored_name' => 'logo-ukf2.png',
                'mime_type' => 'image/png',
                'size' => 123,
                'created_at' => $date,
                'updated_at' => $date
            ],
        ]);
    }
}