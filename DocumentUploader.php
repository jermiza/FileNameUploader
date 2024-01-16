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
        // Define the root path of the USB drive
        $usbDriveRoot = '/Volumes/NO NAME/family_docs';
        // Get the list of folder names in the USB drive
        $folderNames = $this->getFolderNames($usbDriveRoot);

        // Loop through each folder
        foreach ($folderNames as $folderName) {
            // Construct the path to the current folder
            $usbDrivePath = '/Volumes/NO NAME/family_docs/' . $folderName;

            try {
                // Get picture files in the current folder
                $pictures = $this->getPictureFiles($usbDrivePath);

                // Find the corresponding family based on the folder name
                $family = is_numeric($folderName)
                    ? Family::find($folderName)
                    : Family::where('ut_id', $folderName)->first();

                // Loop through each picture and save information in the database
                foreach ($pictures as $picture) {
                    $oldFilename = basename($picture);
                    $newFilename = 'new_' . $oldFilename;

                    // Create a new FamilyDocument instance
                    $familyDocument = new FamilyDocument([
                        'family_id' => $family->id,
                        'old_filename' => $oldFilename,
                        'new_filename' => $newFilename,
                    ]);

                    // Save the FamilyDocument instance to the database
                    $familyDocument->save();
                }

                // Display success message
                $this->info($newFilename . ' uploaded successfully to ' . $family->name);

                // If the folder name is not numeric, update the folder name
                if (!is_numeric($folderName)) {
                    $newFolderName = $family->id;
                    // Rename the folder with the family ID
                    rename($usbDrivePath, '/Volumes/NO NAME/family_docs/' . $newFolderName);
                    $this->info('Folder name updated to: ' . $newFolderName);
                }
            } catch (\Exception $e) {
                // Handle any exceptions and display an error message
                $this->error('Error: ' . $e->getMessage());
            }
        }
    }

    // Helper function to get folder names in a directory
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

    // Helper function to get picture files in a directory and its subdirectories
    private function getPictureFiles($path)
    {
        $pictureExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $files = [];

        foreach ($pictureExtensions as $extension) {
            $pattern = $path . '/*.' . $extension;
            // Merge files matching the pattern
            $files = array_merge($files, glob($pattern));
        }

        // Get subdirectories in the current path
        $subdirectories = array_filter(glob($path . '/*'), 'is_dir');
        foreach ($subdirectories as $subdirectory) {
            // Recursively get picture files in subdirectories
            $subdirectoryPictures = $this->getPictureFiles($subdirectory);
            $files = array_merge($files, $subdirectoryPictures);
        }

        return $files;
    }
}
