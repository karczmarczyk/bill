<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PositionRepository")
 */
class Position
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $netto;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $brutto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\bill", inversedBy="positions")
     */
    private $bill;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNetto(): ?float
    {
        return $this->netto;
    }

    public function setNetto(?float $netto): self
    {
        $this->netto = $netto;

        return $this;
    }

    public function getBrutto(): ?float
    {
        return $this->brutto;
    }

    public function setBrutto(?float $brutto): self
    {
        $this->brutto = $brutto;

        return $this;
    }

    public function getBill(): ?bill
    {
        return $this->bill;
    }

    public function setBill(?bill $bill): self
    {
        $this->bill = $bill;

        return $this;
    }
}
