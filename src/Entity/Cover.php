<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoverRepository")
 * @ORM\Table(name="cover_cover")
 */
class Cover
{
    const WIDTH_SMALL = 200;
    const WIDTH_MEDIUM = 450;
    const WIDTH_LARGE = 700;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;


    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $small;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $medium;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $large;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ark;

    public function __construct()
    {
        $this->small = false;
        $this->medium = false;
        $this->large = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    private function generateFilename() {
        $this->filename = md5($this->getArk()) . ".jpg";
    }
    public function getFilename(): ?string
    {
        if (!isset($this->filename)) {
            $this->generateFilename();
        }
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getSmall(): ?bool
    {
        return $this->small;
    }

    public function setSmall(bool $small): self
    {
        $this->small = $small;

        return $this;
    }

    public function getMedium(): ?bool
    {
        return $this->medium;
    }

    public function setMedium(bool $medium): self
    {
        $this->medium = $medium;

        return $this;
    }

    public function getLarge(): ?bool
    {
        return $this->large;
    }

    public function setLarge(bool $large): self
    {
        $this->large = $large;

        return $this;
    }

    public function getArk(): ?string
    {
        return $this->ark;
    }

    public function setArk(string $ark): self
    {
        $this->ark = $ark;
        $this->generateFilename();
        return $this;
    }

    public function getFilepath($size)
    {
        return __DIR__ . "/../../public/covers/" . $size . "/" . $this->getFilename();
    }

    public function getDerivativeWidth($size)
    {
        switch ($size) {
            case "small":
                return Cover::WIDTH_SMALL;
            case "medium":
                return Cover::WIDTH_MEDIUM;
            case "large":
                return Cover::WIDTH_LARGE;
        }
    }
}
