<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IsbnRepository")
 */
class Isbn
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $value10;

    /**
     * @ORM\Column(type="string", length=13)
     */
    private $value13;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Cover", cascade={"persist", "remove"})
     */
    private $Cover;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue10(): ?string
    {
        return $this->value10;
    }

    public function setValue10(string $value10): self
    {
        $this->value10 = $value10;

        return $this;
    }

    public function getValue13(): ?string
    {
        return $this->value13;
    }

    public function setValue13(string $value13): self
    {
        $this->value13 = $value13;

        return $this;
    }

    public function getCover(): ?Cover
    {
        return $this->Cover;
    }

    public function setCover(?Cover $Cover): self
    {
        $this->Cover = $Cover;

        return $this;
    }
}
