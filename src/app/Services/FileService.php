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
        $folder = trim($subFolder, '/');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $folder . '/' . $fileName;
        $uploadedPath = null;

        try {
            return DB::transaction(function () use ($file, $fullPath, $disk, $folder, $dbLogic, &$uploadedPath) {
                $uploadedPath = $file->storeAs($folder, basename($fullPath), $disk);

                $fileRecord = File::create([
                    'original_name' => $file->getClientOriginalName(),
                    'path'          => $fullPath,
                    'disk'          => $disk,
                    'mime_type'     => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);

                if ($dbLogic) {
                    $dbLogic($fileRecord);
                }

                return $fileRecord;
            });
        }
        catch (Throwable $e) {
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

        return URL::temporarySignedRoute(
            'files.download',
            now()->addMinutes(30),
            ['file' => $file->id]
        );
    }

    public function deleteFile(File $file, ?callable $dbLogic = null): bool
    {
        return DB::transaction(function () use ($file, $dbLogic) {
            Storage::disk($file->disk)->delete($file->path);

            if($dbLogic) {
                $dbLogic();
            }

            return $file->delete();
        });
    }
}