<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NetworkElementRepository")
 */
class NetworkElement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $nome;

    /**
     * @ORM\Column(type="integer")
     */
    private $capacity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StatisticaRete", mappedBy="networkElement", orphanRemoval=true)
     * @ORM\OrderBy({"data"="ASC"})
     */
    private $statisticheRete;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CapacityType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $directoryStatistiche;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $csvCapacityTypeName;

    public function __construct()
    {
        $this->statisticheRete = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return Collection|StatisticaRete[]
     */
    public function getStatisticheRete(): Collection
    {
        return $this->statisticheRete;
    }

    public function addStatisticheRete(StatisticaRete $statisticheRete): self
    {
        if (!$this->statisticheRete->contains($statisticheRete)) {
            $this->statisticheRete[] = $statisticheRete;
            $statisticheRete->setNetworkElement($this);
        }

        return $this;
    }

    public function removeStatisticheRete(StatisticaRete $statisticheRete): self
    {
        if ($this->statisticheRete->contains($statisticheRete)) {
            $this->statisticheRete->removeElement($statisticheRete);
            // set the owning side to null (unless already changed)
            if ($statisticheRete->getNetworkElement() === $this) {
                $statisticheRete->setNetworkElement(null);
            }
        }
        return $this;
    }

    public function _toString(){
        return $this->Nome;
    }

    public function getCapacityType(): ?string
    {
        return $this->CapacityType;
    }

    public function setCapacityType(string $CapacityType): self
    {
        $this->CapacityType = $CapacityType;

        return $this;
    }

    public function getDirectoryStatistiche(): ?string
    {
        return $this->directoryStatistiche;
    }

    public function setDirectoryStatistiche(?string $directoryStatistiche): self
    {
        $this->directoryStatistiche = $directoryStatistiche;

        return $this;
    }

    public function getCsvCapacityTypeName(): ?string
    {
        return $this->csvCapacityTypeName;
    }

    public function setCsvCapacityTypeName(string $csvCapacityTypeName): self
    {
        $this->csvCapacityTypeName = $csvCapacityTypeName;

        return $this;
    }

    public function getLastStatisticValue()
    {
        $value = 0;
        $data = NULL;
        foreach($this->statisticheRete as $statistica){
            if( ($data == NULL) || ($statistica->getData() > $data)){
                $data = $statistica->getData();
                $value = $statistica->getValore();
            }
        }
        return $value;
    }

    public function getFreeCapacity()
    {
        if( ($free = $this->capacity-$this->getLastStatisticValue()) > 0){
            return $free; 
        } else {return 0;}
    }

    public function getFreePercentage()
    {
        if( ($free = $this->capacity-$this->getLastStatisticValue()) > 0){
            return (1-($this->getLastStatisticValue()/$this->capacity))*100; 
        } else {return 0;}
    }

}
