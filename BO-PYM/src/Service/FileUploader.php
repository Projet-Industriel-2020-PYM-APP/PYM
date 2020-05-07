<?php

// src/Service/FileUploader.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;
    private $logger;

    public function __construct(string $targetDirectory, SluggerInterface $slugger, LoggerInterface $logger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->logger = $logger;
    }

    /**
     * Upload a file.
     *
     * The file is uploaded in the directory specified in the services.yml, appended with $directory.
     * If $safe is true, the $name will go through a slugger and will be appended a unique Id.
     *
     * @param UploadedFile $file
     * @param string $name
     * @param string $directory
     * @param bool $safe
     * @return string
     */
    public function upload(UploadedFile $file, string $name, string $directory, bool $safe = true)
    {
        if ($file) {
            $newName = $name;
            if ($safe) {
                $safeFilename = $this->slugger->slug($newName);
                $newName = $safeFilename.'-'.uniqid();
            }
            try {
                if ($file->guessExtension() == "txt" && $directory == "modeles") {
                    $newFileName = $newName . '.' . "babylon";
                    $file->move($this->getTargetDirectory() . $directory, $newFileName);
                } else {
                    $newFileName = $newName . '.' . $file->guessExtension();
                    $file->move($this->getTargetDirectory() . $directory, $newFileName);
                }
                return $newFileName;
            } catch (FileException $e) {
                $this->logger->alert($e);
            }
        }
        return null;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}