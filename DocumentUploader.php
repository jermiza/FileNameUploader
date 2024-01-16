<?php

namespace App\Console\Commands;

use App\Models\Family;
use Illuminate\Support\Str;
use App\Models\FamilyDocument;
use Illuminate\Console\Command;

class DocumentUploader extends Command
{
    protected $signature = 'documents-uploader:upload';

    protected $description = 'Write family documents names in the database';

    public function handle()
    {
        $usbDriveRoot = '/Volumes/NO NAME/family_docs';
        $folderNames = $this->getFolderNames($usbDriveRoot);
    
        foreach ($folderNames as $folderName) {
            $usbDrivePath = '/Volumes/NO NAME/family_docs/' . $folderName;
    
            try {
                $pictures = $this->getPictureFiles($usbDrivePath);
    
                if (is_numeric($folderName)) {
                    $family = Family::find($folderName);
                } else {
                    $family = Family::where('ut_id', $folderName)->first();
                }
    
                foreach ($pictures as $picture) {
                    $oldFilename = basename($picture);
                    $newFilename = 'new_' . $oldFilename;
    
                    $familyDocument = new FamilyDocument([
                        'family_id' => $family->id,
                        'old_filename' => $oldFilename,
                        'new_filename' => $newFilename,
                    ]);
    
                    $familyDocument->save();
                }
    
                $this->info($newFilename . ' uploaded successfully to ' . $family->name);
    
                if (!is_numeric($folderName)) {
                    $newFolderName = $family->id;
                    rename($usbDrivePath, '/Volumes/NO NAME/family_docs/' . $newFolderName);
                    $this->info('Folder name updated to: ' . $newFolderName);
                }
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
            }
        }
    }

    protected function getFolderNames($directory)
    {
        $entries = array_diff(scandir($directory), ['.', '..']);
        $folderNames = [];

        foreach ($entries as $entry) {
            $entryPath = $directory . '/' . $entry;

            if (is_dir($entryPath)) {
                $folderNames[] = $entry;
            }
        }

        return $folderNames;
    }

    private function getPictureFiles($path)
    {
        $pictureExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $files = [];

        foreach ($pictureExtensions as $extension) {
            $pattern = $path . '/*.' . $extension;
            $files = array_merge($files, glob($pattern));
        }
 
        $subdirectories = array_filter(glob($path . '/*'), 'is_dir');
        foreach ($subdirectories as $subdirectory) {
            $subdirectoryPictures = $this->getPictureFiles($subdirectory);
            $files = array_merge($files, $subdirectoryPictures);
        }

        return $files;
    }
}
