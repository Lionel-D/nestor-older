<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    public function __construct(SluggerInterface $slugger, Filesystem $fileSystem)
    {
        $this->slugger = $slugger;
        $this->fileSystem = $fileSystem;
    }

    public function upload(UploadedFile $file, $uploadParams, $replacedFilename = null)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($uploadParams['path'], $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        if ($replacedFilename !== null) {
            $this->remove($replacedFilename, $uploadParams);
        }

        return $fileName;
    }

    public function remove($filename, $uploadParams)
    {
        $this->fileSystem->remove($uploadParams['path'].'/'.$filename);
    }
}
