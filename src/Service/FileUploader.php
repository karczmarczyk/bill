<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = $file->getClientOriginalName();
        $safeFileName = md5($originalFilename . time());

        // Move the file to the directory where file are stored
        try {
            $file->move(
                $this->targetDirectory,
                $safeFileName
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            return false;
        }
        return $safeFileName;
    }

    /**
     * @return mixed
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
