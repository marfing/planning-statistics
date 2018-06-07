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
     * @ORM\Column(type="date")
     */
    private $data;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome_valore;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $elemento_rete;

    
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

    public function getNomeValore(): ?string
    {
        return $this->nome_valore;
    }

    public function setNomeValore(string $nome_valore): self
    {
        $this->nome_valore = $nome_valore;

        return $this;
    }

    public function getElementoRete(): ?string
    {
        return $this->elemento_rete;
    }

    public function setElementoRete(string $elemento_rete): self
    {
        $this->elemento_rete = $elemento_rete;

        return $this;
    }
}
