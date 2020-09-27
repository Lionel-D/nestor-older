<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
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

    /**
     * @var Imagine
     */
    private $imagine;

    public function __construct(SluggerInterface $slugger, Filesystem $fileSystem)
    {
        $this->slugger = $slugger;
        $this->fileSystem = $fileSystem;
        $this->imagine = new Imagine();
    }

    public function upload(UploadedFile $file, $uploadParams, $replacedFilename = null)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($uploadParams['path'], $fileName);
        } catch (FileException $fileException) {
            return false;
        }

        foreach ($uploadParams['formats'] as $format) {
            $this->resize($uploadParams['path'], $fileName, $format);
        }

        if ($replacedFilename !== null) {
            $this->remove($replacedFilename, $uploadParams);
        }

        return $fileName;
    }

    public function resize($path, $fileName, $format)
    {
        $mode = ImageInterface::THUMBNAIL_OUTBOUND;

        $image = $this->imagine->open($path.'/'.$fileName);

        $formatName = $fileName;

        if ($format['prefix'] !== '') {
            $formatName = $format['prefix'].'_'.$formatName;
        }

        $image->thumbnail(new Box($format['width'], $format['height']), $mode)->save($path.'/'.$formatName);
    }

    public function remove($fileName, $uploadParams)
    {
        foreach ($uploadParams['formats'] as $format) {
            $formatName = $fileName;

            if ($format['prefix'] !== '') {
                $formatName = $format['prefix'].'_'.$formatName;
            }

            $this->fileSystem->remove($uploadParams['path'].'/'.$formatName);
        }
    }
}
