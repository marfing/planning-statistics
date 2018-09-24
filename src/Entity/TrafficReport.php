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
     * @ORM\Column(type="string", length=255)
     */
    private $routerInIP;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $routerOutName="Missing";

    /**
     * @ORM\Column(type="string", length=255)
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

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $samples=1;

    
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

    public function getRouterInIP(): ?string
    {
        return $this->routerInIP;
    }

    public function setRouterInIP(string $routerInIP): self
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

    public function getRouterOutIP(): ?string
    {
        return $this->routerOutIP;
    }

    public function setRouterOutIP(string $routerOutIP): self
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
        $this->bandwidth = $bandwidth/1000000; // saved as Mega bps
        return $this;
    }

    public function setMegaBandwidth(?int $bandwidth): self
    {
        $this->bandwidth = $bandwidth; // saved as Mega bps
        return $this;
    }

    public function setBandwidthAsString(?string $bandwidth): self
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
        if($routerOut != NULL){
            $this->setRouterOutName($routerOut->getName());
        }

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

    public function addBpsToBw(int $_bw){
        //echo("<p>TrafficReport::addBpsToBw - oldbw: ".$this->bandwidth." - addbw: ".$_bw."</p>");
        $this->bandwidth += $_bw/1000000; //always consider Mega bps
        //echo("<p>TrafficReport::addBpsToBw - newbw: ".$this->bandwidth."</p>");
    }

    public function getSamples(): ?int
    {
        if(!$this->samples){
            return 1;
        }
        return $this->samples;
    }

    public function setSamples(?int $samples): self
    {
        $this->samples = $samples;

        return $this;
    }

    public function addSample(int $sampleBw)
    {
        $this->samples++;
        $this->bandwidth += $sampleBw; 
        return $this;
    }

    public function getAverageBw(): ?int
    {
        return $this->averageBw;
    }

    public function setAverageBw(?int $averageBw): self
    {
        $this->averageBw = $averageBw;

        return $this;
    }
}
