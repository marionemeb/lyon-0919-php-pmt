<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InsuranceRepository")
 */
class Insurance
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
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Inscription", mappedBy="insurance")
     */
    private $inscription;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return strval($this->getName());
    }

    public function __construct()
    {
        $this->inscription = new ArrayCollection();
    }

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscription(): Collection
    {
        return $this->inscription;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscription->contains($inscription)) {
            $this->inscription[] = $inscription;
            $inscription->setInsurance($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscription->contains($inscription)) {
            $this->inscription->removeElement($inscription);
            // set the owning side to null (unless already changed)
            if ($inscription->getInsurance() === $this) {
                $inscription->setInsurance(null);
            }
        }

        return $this;
    }
}
