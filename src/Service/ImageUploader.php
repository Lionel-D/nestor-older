<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ImageUploader
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

    public function __construct(SluggerInterface $slugger, Filesystem $filesystem)
    {
        $this->slugger = $slugger;
        $this->fileSystem = $filesystem;
        $this->imagine = new Imagine();
    }

    /**
     * @param UploadedFile|null $uploadedFile
     * @param mixed[]           $uploadParams
     * @param string|null       $replacedFilename
     *
     * @return string|null
     */
    public function upload($uploadedFile, $uploadParams, $replacedFilename = null)
    {
        if (null === $uploadedFile || null === $uploadedFile->getClientOriginalName()) {
            return null;
        }

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        try {
            $uploadedFile->move($uploadParams['path'], $fileName);
        } catch (FileException $fileException) {
            return null;
        }

        foreach ($uploadParams['formats'] as $format) {
            $this->resize($uploadParams['path'], $fileName, $format);
        }

        if (null !== $replacedFilename) {
            $this->remove($replacedFilename, $uploadParams);
        }

        return $fileName;
    }

    /**
     * @param string  $path
     * @param string  $fileName
     * @param mixed[] $format
     */
    public function resize($path, $fileName, $format): void
    {
        $mode = ImageInterface::THUMBNAIL_OUTBOUND;

        $image = $this->imagine->open($path.'/'.$fileName);

        $formatName = $fileName;

        if ('' !== $format['prefix']) {
            $formatName = $format['prefix'].'_'.$formatName;
        }

        $image->thumbnail(new Box($format['width'], $format['height']), $mode)->save($path.'/'.$formatName);
    }

    /**
     * @param string  $fileName
     * @param mixed[] $uploadParams
     */
    public function remove($fileName, $uploadParams): void
    {
        foreach ($uploadParams['formats'] as $format) {
            $formatName = $fileName;

            if ('' !== $format['prefix']) {
                $formatName = $format['prefix'].'_'.$formatName;
            }

            $this->fileSystem->remove($uploadParams['path'].'/'.$formatName);
        }
    }
}
