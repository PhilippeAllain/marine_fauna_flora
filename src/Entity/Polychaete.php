<?php

namespace App\Entity;

use App\Repository\PolychaeteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;


#[ORM\Entity(repositoryClass: PolychaeteRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Ce polychète existe déjà dans le glossaire.')]
#[Vich\Uploadable()]
class Polychaete  
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Le mot doit comporter au moins {{ limit }} caractères.', maxMessage: 'Le mot ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\NotBlank(message: 'Le mot ne peut pas être vide.')]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Le mot doit comporter au moins {{ limit }} caractères.', maxMessage: 'Le mot ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\NotBlank(message: 'Le mot ne peut pas être vide.')]
    private ?string $dci = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 5, minMessage: 'La définition doit comporter au moins {{ limit }} caractères.')]
    #[Assert\NotBlank(message: 'La définition ne peut pas être vide.')]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[Vich\UploadableField(mapping: 'polychaetes', fileNameProperty: 'thumbnail')]
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif'],
        mimeTypesMessage: 'Veuillez télécharger une image au format JPEG, PNG ou GIF.',
        maxSizeMessage: 'La taille de l\'image ne doit pas dépasser {{ limit }}.',
    )]
    private ?File $thumbnailFile = null;

    #[ORM\Column(length: 550, nullable: true)]
    #[Assert\Url(message: 'Veuillez entrer une URL valide.')]
    private ?string $url = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDci(): ?string
    {
        return $this->dci;
    }

    public function setDci(string $dci): static
    {
        $this->dci = $dci;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of thumbnailFile
     */ 
    public function getThumbnailFile(): ?File
    {
        return $this->thumbnailFile;
    }

 
    public function setThumbnailFile(File $thumbnailFile): static
    {
        $this->thumbnailFile = $thumbnailFile;

        return $this;
    }
}
