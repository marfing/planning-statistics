<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RouterRepository")
 */
class Router
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
    private $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\Ip
     */
    private $ipAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileSystemRootPath;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrafficReport", mappedBy="routerIn")
     */
    private $flowsIN;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrafficReport", mappedBy="routerOut")
     */
    private $flowsOut;

    public function __construct()
    {
        $this->flowsIN = new ArrayCollection();
        $this->flowsOut = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getFileSystemRootPath(): ?string
    {
        return $this->fileSystemRootPath;
    }

    public function setFileSystemRootPath(?string $fileSystemRootPath): self
    {
        $this->fileSystemRootPath = $fileSystemRootPath;

        return $this;
    }

    /**
     * @return Collection|TrafficReport[]
     */
    public function getFlowsIN(): Collection
    {
        return $this->flowsIN;
    }

    public function addFlowsIN(TrafficReport $flowsIN): self
    {
        if (!$this->flowsIN->contains($flowsIN)) {
            $this->flowsIN[] = $flowsIN;
            $flowsIN->setRouterIn($this);
        }

        return $this;
    }

    public function removeFlowsIN(TrafficReport $flowsIN): self
    {
        if ($this->flowsIN->contains($flowsIN)) {
            $this->flowsIN->removeElement($flowsIN);
            // set the owning side to null (unless already changed)
            if ($flowsIN->getRouterIn() === $this) {
                $flowsIN->setRouterIn(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrafficReport[]
     */
    public function getFlowsOut(): Collection
    {
        return $this->flowsOut;
    }

    public function addFlowsOut(TrafficReport $flowsOut): self
    {
        if (!$this->flowsOut->contains($flowsOut)) {
            $this->flowsOut[] = $flowsOut;
            $flowsOut->setRouterOut($this);
        }

        return $this;
    }

    public function removeFlowsOut(TrafficReport $flowsOut): self
    {
        if ($this->flowsOut->contains($flowsOut)) {
            $this->flowsOut->removeElement($flowsOut);
            // set the owning side to null (unless already changed)
            if ($flowsOut->getRouterOut() === $this) {
                $flowsOut->setRouterOut(null);
            }
        }

        return $this;
    }
}
