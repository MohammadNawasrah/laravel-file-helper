<?php

namespace Nawasrah\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Exception;

class FileHelper
{
    protected $path;
    protected $isFolder = false;

    public function __construct($path = null)
    {
        $this->path = $path;
    }

    /**
     * Set the path and check if it's a folder.
     */
    public static function isFolder($path)
    {
        try {
            $instance = new self($path);
            $instance->isFolder = File::isDirectory($path);

            return $instance;
        } catch (Exception $e) {
            throw new Exception("Error checking if path is a folder: " . $e->getMessage());
        }
    }

    /**
     * Set the path for the instance (for chainability).
     */
    public static function setPath($path)
    {
        try {
            return new self($path);
        } catch (Exception $e) {
            throw new Exception("Error setting path: " . $e->getMessage());
        }
    }

    /**
     * Upload a file.
     */
    public function upload(UploadedFile $file, $subfolder = null)
    {
        try {
            $folder = $this->getFolderPath($subfolder);
            $fileName = time() . '_' . $file->getClientOriginalName();

            $file->move($folder, $fileName);

            return $folder . DIRECTORY_SEPARATOR . $fileName;
        } catch (Exception $e) {
            throw new Exception("Error uploading file: " . $e->getMessage());
        }
    }

    /**
     * Delete a file.
     */
    public static function delete($filePath)
    {
        try {
            if (File::exists($filePath)) {
                return File::delete($filePath);
            }
            return false;
        } catch (Exception $e) {
            throw new Exception("Error deleting file: " . $e->getMessage());
        }
    }

    /**
     * Replace a file.
     */
    public function replace($existingFilePath, UploadedFile $newFile, $subfolder = null)
    {
        try {
            self::delete($existingFilePath);
            return $this->upload($newFile, $subfolder);
        } catch (Exception $e) {
            throw new Exception("Error replacing file: " . $e->getMessage());
        }
    }

    /**
     * Check if file exists.
     */
    public static function fileExists($filePath)
    {
        try {
            return File::exists($filePath);
        } catch (Exception $e) {
            throw new Exception("Error checking if file exists: " . $e->getMessage());
        }
    }

    /**
     * Check if folder exists.
     */
    public static function folderExists($folderPath)
    {
        try {
            return File::isDirectory($folderPath);
        } catch (Exception $e) {
            throw new Exception("Error checking if folder exists: " . $e->getMessage());
        }
    }

    /**
     * Get file name.
     */
    public static function getFileName($filePath)
    {
        try {
            return File::name($filePath);
        } catch (Exception $e) {
            throw new Exception("Error getting file name: " . $e->getMessage());
        }
    }

    /**
     * Get file extension.
     */
    public static function getFileExtension($filePath)
    {
        try {
            return File::extension($filePath);
        } catch (Exception $e) {
            throw new Exception("Error getting file extension: " . $e->getMessage());
        }
    }

    /**
     * Get file size.
     */
    public static function getFileSize($filePath)
    {
        try {
            return File::size($filePath);
        } catch (Exception $e) {
            throw new Exception("Error getting file size: " . $e->getMessage());
        }
    }

    /**
     * Get files from a directory.
     */
    public static function getFilesFromDir($dirPath)
    {
        try {
            return File::exists($dirPath) ? File::files($dirPath) : [];
        } catch (Exception $e) {
            throw new Exception("Error getting files from directory: " . $e->getMessage());
        }
    }

    /**
     * Get folders and files as JSON.
     */
    public function getDirContentsAsJson()
    {
        try {
            if (!$this->isFolder) {
                throw new Exception("The provided path is not a valid folder.");
            }

            if (!File::exists($this->path)) {
                throw new Exception("Folder does not exist.");
            }

            $directories = File::directories($this->path);
            $files = File::files($this->path);

            return [
                'folders' => array_map(fn($dir) => basename($dir), $directories),
                'files' => array_map(fn($file) => $file->getFilename(), $files),
            ];
        } catch (Exception $e) {
            throw new Exception("Error retrieving directory contents: " . $e->getMessage());
        }
    }

    /**
     * Check if the current path exists.
     */
    public function exists()
    {
        try {
            return File::exists($this->path);
        } catch (Exception $e) {
            throw new Exception("Error checking if path exists: " . $e->getMessage());
        }
    }

    /**
     * Utility: Get the folder path.
     */
    private function getFolderPath($subfolder)
    {
        try {
            $folder = $this->path . ($subfolder ? DIRECTORY_SEPARATOR . $subfolder : '');
            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
            }
            return $folder;
        } catch (Exception $e) {
            throw new Exception("Error creating folder: " . $e->getMessage());
        }
    }
}
