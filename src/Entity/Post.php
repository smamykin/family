<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostPhoto", mappedBy="post", orphanRemoval=true)
     */
    private $postPhotos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostSetting", mappedBy="post", orphanRemoval=true)
     */
    private $postSettings;

    public function __construct()
    {
        $this->postPhotos = new ArrayCollection();
        $this->postSettings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|PostPhoto[]
     */
    public function getPostPhotos(): Collection
    {
        return $this->postPhotos;
    }

    public function addPostPhoto(PostPhoto $postPhoto): self
    {
        if (!$this->postPhotos->contains($postPhoto)) {
            $this->postPhotos[] = $postPhoto;
            $postPhoto->setPost($this);
        }

        return $this;
    }

    public function removePostPhoto(PostPhoto $postPhoto): self
    {
        if ($this->postPhotos->contains($postPhoto)) {
            $this->postPhotos->removeElement($postPhoto);
            // set the owning side to null (unless already changed)
            if ($postPhoto->getPost() === $this) {
                $postPhoto->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostSetting[]
     */
    public function getPostSettings(): Collection
    {
        return $this->postSettings;
    }

    public function addPostSetting(PostSetting $postSetting): self
    {
        if (!$this->postSettings->contains($postSetting)) {
            $this->postSettings[] = $postSetting;
            $postSetting->setPost($this);
        }

        return $this;
    }

    public function removePostSetting(PostSetting $postSetting): self
    {
        if ($this->postSettings->contains($postSetting)) {
            $this->postSettings->removeElement($postSetting);
            // set the owning side to null (unless already changed)
            if ($postSetting->getPost() === $this) {
                $postSetting->setPost(null);
            }
        }

        return $this;
    }
}
