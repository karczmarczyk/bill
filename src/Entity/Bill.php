<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BillRepository")
 */
class Bill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shop;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $summary_brutto;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $summary_netto;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Position", mappedBy="bill", cascade={"persist"})
     */
    private $positions;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShop(): ?string
    {
        return $this->shop;
    }

    public function setShop(string $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSummaryBrutto(): ?float
    {
        return $this->summary_brutto;
    }

    public function setSummaryBrutto(?float $summary_brutto): self
    {
        $this->summary_brutto = $summary_brutto;

        return $this;
    }

    public function getSummaryNetto(): ?float
    {
        return $this->summary_netto;
    }

    public function setSummaryNetto(?float $summary_netto): self
    {
        $this->summary_netto = $summary_netto;

        return $this;
    }

    /**
     * @return Collection|Position[]
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(Position $position): self
    {
        if (!$this->positions->contains($position)) {
            $this->positions[] = $position;
            $position->setBill($this);
        }

        return $this;
    }

    public function removePosition(Position $position): self
    {
        if ($this->positions->contains($position)) {
            $this->positions->removeElement($position);
            // set the owning side to null (unless already changed)
            if ($position->getBill() === $this) {
                $position->setBill(null);
            }
        }

        return $this;
    }
}
