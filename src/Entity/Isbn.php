<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IsbnRepository")
 * @ORM\Table(name="cover_isbn")
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

    private function cleanIsbn(string $isbn)
    {
        return preg_replace("#[^\dX]#", "", strtoupper($isbn));
    }

    private $faleIsbn;

    public function __construct(string $isbn)
    {
        $this->faleIsbn = new \Isbn\Isbn();
        $this->setIsbn($isbn);
    }

    private function setIsbn(string $isbn)
    {
        $isbn = $this->clean($isbn);
        if (strlen($isbn) == 10) {
            $this->setValue10($isbn);
            $this->setValue13($this->faleIsbn->translate->to13($isbn));
        } elseif (strlen($isbn) == 13) {
            $this->setValue13($isbn);
            $this->setValue10($this->faleIsbn->translate->to10($isbn));
        } else {
            dd("PAS ISBN : " . strlen($isbn));
        }
    }

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

    public function hasCoverPathForSize(string $size) {
        if (is_null($this->getCover())) {
            return false;
        }
        switch ($size) {
            case "small":
                return $this->getCover()->getSmall();
            case "medium":
                return $this->getCover()->getMedium();
            case "large":
                return $this->getCover()->getLarge();
        }

        dd($this->getCover());
    }

    public function setCover(?Cover $Cover): self
    {
        $this->Cover = $Cover;

        return $this;
    }

    function isbn10_to_13($isbn)
    {
        $isbn = trim($isbn);
        if (strlen($isbn) == 12) { // if number is UPC just add zero
            $isbn13 = '0' . $isbn;
        } else {
            $isbn2 = substr("978" . trim($isbn), 0, -1);
            $sum13 = $this->isbn13_checksum($isbn2);
            $isbn13 = "$isbn2$sum13";
        }
        return ($isbn13);
    }

    public function clean($isbn)
    {
        $output = $isbn;
        $output = strtoupper($output);
        $output = preg_replace("/[^\dX]/", "", $output);
        return $output;
    }

    function isbn13_checksum($isbn)
    {
        $sum = 0;
        $isbn = str_split(preg_replace('/[^\d]/', '', $isbn));
        foreach ($isbn as $key => $z) {
            if ($key >= 12) break;
            $sum += ($key % 2) ? $z * 3 : $z;
        }
        $checksum = (10 - $sum % 10);
        return ($checksum == 10) ? 0 : $checksum;
    }

    private function isbn13_to_10($isbn)
    {
        if (\is_string($isbn) === false) {
            throw new Exception('Invalid parameter type.');
        }

        //Verify length
        $isbnLength = \strlen($isbn);
        if ($isbnLength < 9 or $isbnLength > 10) {
            throw new Exception('Invalid ISBN-10 format.');
        }

        //Calculate check digit
        $check = 0;
        for ($i = 0; $i < 9; $i++) {
            if ($isbn[$i] === 'X') {
                $check += 10 * \intval(10 - $i);
            } else {
                $check += \intval($isbn[$i]) * \intval(10 - $i);
            }
        }

        $check = 11 - $check % 11;
        if ($check === 10) {
            return 'X';
        } elseif ($check === 11) {
            return 0;
        }

        return $check;
    }
}
