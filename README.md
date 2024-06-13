---

# Laravel Document Uploader Command

## Overview

This Laravel console command, `documents-uploader:upload`, is a versatile tool designed for efficiently handling the upload of various types of documents, including pictures, PDFs, and more, into the database. It is capable of processing large files (e.g., 20GB, 30GB) and reads folder structures, extracting file names, and storing them in the database based on folder names.

## Features

- **Document Variety**: Supports the upload of various document types, such as pictures, PDFs, and more.
- **Large File Handling**: Capable of efficiently handling large files up to 20GB, 30GB, etc.
- **Folder Structure Parsing**: Reads folder structures, extracting document names, and organizes them in the database.

## Usage

```bash
php artisan documents-uploader:upload
```

## Requirements

- Laravel 5.x or higher
- PHP 7.x or higher

## Installation

1. Clone this repository:

   ```bash
   git clone https://github.com/jermiza/FileNameUploader.git
    
   ```
   
   or run command:
   
      ```bash 
    php artisan make:command DocumentUploader
   ```

3. Configure USB drive root path:

   Update the `$usbDriveRoot` variable in the `DocumentUploader` class with the appropriate USB drive root path.

4. Run the command:

   ```bash
   php artisan documents-uploader:upload
   ```

## Notes

- Ensure proper permissions for reading from and writing to the specified USB drive path.
- This command is optimized for handling large files efficiently, but consider server capabilities and constraints.
- Handle exceptions gracefully for error scenarios.

## License

This Laravel command is open-sourced software licensed under the [MIT license](LICENSE.md).

---

Feel free to further modify or customize the details as needed for your specific use case.
