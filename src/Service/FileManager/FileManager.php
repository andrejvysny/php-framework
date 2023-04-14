<?php

namespace App\Service\FileManager;

use App\Entity\File;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    private Request $request;

    /**
     * @throws ORMException
     */
    public function __construct(
        private readonly string $targetDirectory,
        private readonly SluggerInterface $slugger,
        private EntityManager $entityManager,
        RequestStack $requestStack
    )
    {
        if (!$this->entityManager->isOpen()) {
            $this->entityManager = $this->entityManager::create(
                $this->entityManager->getConnection(),
                $this->entityManager->getConfiguration()
            );
        }
        $this->request = $requestStack->getCurrentRequest();
    }


    public function upload(UploadedFile $file, bool $remove = false): ?File
    {
        // get original name with extension
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // get original name without extension
        $safeFilename = $this->slugger->slug($originalFilename);

        // get extension
        $extension = $file->guessExtension();

        // Create uploaded filename
        $path = $this->generateUniqId() . '.' . $file->guessExtension();

        // Check if filename don't exist
        do {
            $exist = $this->entityManager->getRepository(File::class)->findOneBy(['path' => $path]);
            $path = $this->generateUniqId() . '.' . $file->guessExtension();
        } while ($exist);

        $this->checkTargetDirectory($this->getTargetDirectory());

        try {
            $file->move($this->getTargetDirectory(), $path);
        } catch (FileException $e) {
            //TODO: ... handle exception if something happens during file upload
        }

        try {
            $UploadedFile = $this->createRecord($safeFilename, $extension, $path, $remove);
        } catch (Exception $exception) {
            throw new \LogicException($exception->getMessage(), $exception->getCode());
        }

        return $UploadedFile;
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function createRecord(string $name, string $extension, string $path, bool $remove = false): File
    {
        $file = new File();
        $file->setName($this->parseFileName($name));
        $file->setExtension($extension);
        $file->setPath($path);
        $file->setUrl(
            $this->request->getSchemeAndHttpHost() . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $path
        );
        $file->setRemove($remove);
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        $UploadedFile = $this->entityManager->getRepository(File::class)->findOneBy(['path' => $path]);

        if (!$UploadedFile) {
            throw new \RuntimeException('Record of file was not created', 500);
        }

        return $UploadedFile;
    }


    public function getTargetDirectory(): string
    {
        return rtrim($this->targetDirectory, '/') . DIRECTORY_SEPARATOR;
    }


    public function generateUniqId(int $maxLength = 240): string
    {
        return substr(str_shuffle(uniqid('', true)) . uniqid('', true) . '.' . time(), 0, $maxLength);
    }

    public function checkTargetDirectory(string $directory): void
    {
        if (!file_exists($directory) && !mkdir($directory) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }
    }

    private function parseFileName(string $filename): string
    {

        $options = [
            ' ' => '_',
            '-' => '_'
        ];
        return strtr($filename, $options);
    }
}
