<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatisticaReteRepository")
 */
class StatisticaRete
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $valore;

    /**
     * @ORM\Column(type="date", unique=true)
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NetworkElement", inversedBy="statisticheRete")
     * @ORM\JoinColumn(nullable=false)
     */
    private $networkElement;

    
    public function getId()
    {
        return $this->id;
    }

    public function getValore(): ?int
    {
        return $this->valore;
    }

    public function setValore(int $valore): self
    {
        $this->valore = $valore;

        return $this;
    }

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(\DateTimeInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getNetworkElement(): ?NetworkElement
    {
        return $this->networkElement;
    }

    public function setNetworkElement(?NetworkElement $networkElement): self
    {
        $this->networkElement = $networkElement;

        return $this;
    }
}
