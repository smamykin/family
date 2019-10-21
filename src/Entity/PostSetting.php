<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostSettingRepository")
 */
class PostSetting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SettingOption")
     * @ORM\JoinColumn(nullable=false)
     */
    private $settingOption;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="postSettings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSettingOption(): ?SettingOption
    {
        return $this->settingOption;
    }

    public function setSettingOption(?SettingOption $settingOption): self
    {
        $this->settingOption = $settingOption;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
