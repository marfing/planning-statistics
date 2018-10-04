<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ComuneRepository")
 */
class Comune
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
    private $denominazione;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codiceComune;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codiceCittaMetropolitana;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $denominazioneCittaMetropolitana;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Copertura", mappedBy="comune")
     */
    private $coperture;

    public function __construct()
    {
        $this->coperture = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenominazione(): ?string
    {
        return $this->denominazione;
    }

    public function setDenominazione(string $denominazione): self
    {
        $this->denominazione = $denominazione;

        return $this;
    }

    public function getCodiceComune(): ?string
    {
        return $this->codiceComune;
    }

    public function setCodiceComune(string $codiceComune): self
    {
        $this->codiceComune = $codiceComune;

        return $this;
    }

    public function getCodiceCittaMetropolitana(): ?string
    {
        return $this->codiceCittaMetropolitana;
    }

    public function setCodiceCittaMetropolitana(?string $codiceCittaMetropolitana): self
    {
        $this->codiceCittaMetropolitana = $codiceCittaMetropolitana;

        return $this;
    }

    public function getDenominazioneCittaMetropolitana(): ?string
    {
        return $this->denominazioneCittaMetropolitana;
    }

    public function setDenominazioneCittaMetropolitana(?string $denominazioneCittaMetropolitana): self
    {
        $this->denominazioneCittaMetropolitana = $denominazioneCittaMetropolitana;

        return $this;
    }

    /**
     * @return Collection|Copertura[]
     */
    public function getCoperture(): Collection
    {
        return $this->coperture;
    }

    public function addCoperture(Copertura $coperture): self
    {
        if (!$this->coperture->contains($coperture)) {
            $this->coperture[] = $coperture;
            $coperture->setComune($this);
        }

        return $this;
    }

    public function removeCoperture(Copertura $coperture): self
    {
        if ($this->coperture->contains($coperture)) {
            $this->coperture->removeElement($coperture);
            // set the owning side to null (unless already changed)
            if ($coperture->getComune() === $this) {
                $coperture->setComune(null);
            }
        }

        return $this;
    }
}
