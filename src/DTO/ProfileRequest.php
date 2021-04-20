<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class ProfileRequest
{
    /**
     * @Assert\NotBlank()
     */
    private string $firstName;
    private string $lastName;

    /**
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/jpg", "image/png"},
     *     maxSize= "2M"
     * )
     * @Assert\NotNull()
     */
    private ?UploadedFile $file;

    public function __construct(Request $request) {
        $files = $request->files->get('profile_request');
        $this->file = isset($files['file']) && $files['file'] instanceof UploadedFile ? $files['file'] : null;
        if ($request->request->get('profile_request')) {
            $this->firstName = $request->request->get('profile_request')['firstName'];
            $this->lastName = $request->request->get('profile_request')['lastName'];
        }
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return ?UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param ?UploadedFile $file
     */
    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }


}