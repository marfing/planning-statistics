<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Datetime;

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
     * @ORM\Column(type="date", unique=false)
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

    public function getDataAsString()
    {
        return $this->data->format('Y-m-d');
    }

    public function setData(\DateTimeInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    //data nel formato anno-mese-giorno
    public function setDataFromString( $stringDate )
    {
        $tempData = new DateTime();
        $pieces = explode("-",$stringDate);
        $tempData->setDate($pieces[0],$pieces[1],$pieces[2]);
        $this->data = $tempData;

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
