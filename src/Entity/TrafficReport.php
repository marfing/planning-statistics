<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrafficReportRepository")
 */
class TrafficReport
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
    private $routerInName;

    /**
     * @ORM\Column(type="integer")
     */
    private $routerInIP;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $routerOutName;

    /**
     * @ORM\Column(type="integer")
     */
    private $routerOutIP;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bandwidth;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastTimestamp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Router", inversedBy="flowsIN")
     * @ORM\JoinColumn(nullable=false)
     */
    private $routerIn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Router", inversedBy="flowsOut")
     * @ORM\JoinColumn(nullable=false)
     */
    private $routerOut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    public function getId()
    {
        return $this->id;
    }

    public function getRouterInName(): ?string
    {
        return $this->routerInName;
    }

    public function setRouterInName(?string $routerInName): self
    {
        $this->routerInName = $routerInName;

        return $this;
    }

    public function getRouterInIP(): ?int
    {
        return $this->routerInIP;
    }

    public function setRouterInIP(int $routerInIP): self
    {
        $this->routerInIP = $routerInIP;

        return $this;
    }

    public function getRouterOutName(): ?string
    {
        return $this->routerOutName;
    }

    public function setRouterOutName(?string $routerOutName): self
    {
        $this->routerOutName = $routerOutName;

        return $this;
    }

    public function getRouterOutIP(): ?int
    {
        return $this->routerOutIP;
    }

    public function setRouterOutIP(int $routerOutIP): self
    {
        $this->routerOutIP = $routerOutIP;

        return $this;
    }

    public function getBandwidth(): ?int
    {
        return $this->bandwidth;
    }

    public function setBandwidth(?int $bandwidth): self
    {
        $this->bandwidth = $bandwidth;

        return $this;
    }

    public function getLastTimestamp(): ?\DateTimeInterface
    {
        return $this->lastTimestamp;
    }

    public function setLastTimestamp(\DateTimeInterface $lastTimestamp): self
    {
        $this->lastTimestamp = $lastTimestamp;

        return $this;
    }

    public function getRouterIn(): ?Router
    {
        return $this->routerIn;
    }

    public function setRouterIn(?Router $routerIn): self
    {
        $this->routerIn = $routerIn;

        return $this;
    }

    public function getRouterOut(): ?Router
    {
        return $this->routerOut;
    }

    public function setRouterOut(?Router $routerOut): self
    {
        $this->routerOut = $routerOut;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
