<?php

namespace App\Entity;

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
     * @ORM\Column(type="string", length=255)
     */
    private $Nome;

    /**
     * @ORM\Column(type="integer")
     */
    private $Capacit;

    public function getId()
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->Nome;
    }

    public function setNome(string $Nome): self
    {
        $this->Nome = $Nome;

        return $this;
    }

    public function getCapacit(): ?int
    {
        return $this->Capacit;
    }

    public function setCapacit(int $Capacit): self
    {
        $this->Capacit = $Capacit;

        return $this;
    }
}
