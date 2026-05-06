<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class FileService
{
    public function uploadAndCreateRecord(
        UploadedFile $file, 
        string $subFolder = '', 
        string $disk = 'public', 
        callable $dbLogic = null
    ): File {
        $basePath = $this->diskPaths[$disk] ?? 'uploads';
        $folder = trim($basePath . '/' . $subFolder, '/');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $folder . '/' . $fileName;
        
        $uploadedPath = null;

        try {
            return DB::transaction(function () use ($file, $fullPath, $disk, $folder, $dbLogic, &$uploadedPath) {
                // 1. Nahrať fyzický súbor
                $uploadedPath = $file->storeAs($folder, basename($fullPath), $disk);

                // 2. Vytvoriť záznam v tabuľke 'files'
                $fileRecord = File::create([
                    'original_name' => $file->getClientOriginalName(),
                    'path'          => $fullPath,
                    'disk'          => $disk,
                    'mime_type'     => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);

                // 3. Spustiť externú logiku (napr. priradenie k Userovi), ak bola poskytnutá
                if ($dbLogic) {
                    $dbLogic($fileRecord);
                }

                return $fileRecord;
            });
        } catch (Throwable $e) {
            // Ak zlyhal upload, DB zápis súboru ALEBO vaša externá logika, upraceme disk
            if ($uploadedPath) {
                Storage::disk($disk)->delete($uploadedPath);
            }
            throw $e;
        }
    }

    public function getUrl(File $file): string
    {
        if ($file->disk === 'public') {
            return Storage::url($file->path);
        }

        // Pre súkromné disky vygenerujeme "signed URL" na náš vlastný controller
        // Funguje to aj na local driveri bez nutnosti S3 konfigurácie
        return URL::temporarySignedRoute(
            'files.download', // Názov routy (vytvoríme v kroku 2)
            now()->addMinutes(30),
            ['file' => $file->id]
        );
    }

    public function deleteFile(File $file): bool
    {
        return DB::transaction(function () use ($file) {
            Storage::disk($file->disk)->delete($file->path);
            return $file->delete();
        });
    }
}