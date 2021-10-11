<?php

namespace App\Entity;

use App\Repository\SocietyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SocietyRepository::class)
 */
class Society
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $name;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="societies")
     */
    public $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /*
     * =============================================
     *      Définition des setters de la classe
     * =============================================
     */

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }



    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /*
     * =============================================
     *      Définition des getters de la classe
     * =============================================
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }



    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
