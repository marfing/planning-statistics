<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use \Datetime;

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

    public function amIRouterIN(string $sourceIP){
        //dump($sourceIP);
        //echo("<p>SourceIp passed to amIRouterIn: -".$sourceIP."-</p>");
        if(filter_var($sourceIP,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)==false){
            return false;
        }
        $command="python /home/mau/quagga/showipbgp.py -ip ".$sourceIP;
        //echo("<p>python command: ".$command."</p><br><br>");
        $routerHandlerIP = shell_exec($command);
        //echo("<p>amIrouterIN Answer IP: ".$routerHandlerIP. "</p>");
        //dump($routerHandlerIP);
        //dump($this->ipAddress);
        //echo("<p>Compare Ip address: ThisRouter: ".$this->ipAddress." - RouterIn: ".$routerHandlerIP."</p>");
        if(trim($routerHandlerIP)===$this->ipAddress){
            //echo("<p>Compare ok</p>");
            return true;
        }
        return false;
    }

    public function getRouterOut(string $destinationIP){
        //implementare qui chiamata a sf di maurizio per avere IP address del router di terminazione per questo IP
        //echo("<p>DestIp passed to getRouterOut: -".$destinationIP."-</p>");
        if(filter_var($destinationIP,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)==false){
            return "0.0.0.100";
        }
        $command="python /home/mau/quagga/showipbgp.py -ip ".$destinationIP;
        //echo("<p>getRouterOut python command: ".$command."</p><br><br>");
        $routerHandlerIP = shell_exec($command);
        //echo("<p>getRouterOut Answer IP: ".$routerHandlerIP. "</p><br><br>");
        //dump($routerHandlerIP);
        return trim($routerHandlerIP);
    }

    public function getExistingFlowIn(DateTime $time, string $routerOutIP)
    {
        //echo("<p>getExistingFlowIn - RouterOutIP: ".$routerOutIP." - Time: ".$time->format('Y-m-d H:i:s')."</p>");
        foreach($this->flowsIN as $report){
            //echo("<p>getExistingFlowIn - Report matching check - Report out router: ".$report->getRouterOutIP()." - Report time: ".$report->getLastTimestamp()->format(('Y-m-d H:i:s'))."</p>");
            if ($report->getRouterOutIP() === $routerOutIP 
                && $report->getLastTimestamp() === $time){
                //echo("getExistingFlowIn -Matching OK");
                    return $report;
            } 
        }
        //echo("<p>getExistingFlowIn -Matching NOK</p>");
        return NULL;
    }
    
    
    public function getTableArray(DateTime $time,string $tableTime): array 
    {
        //echo("<p><b>Router::getTableArray - Time: ".$time->format('Y-m-d H:i:s')." - Table time: ".$tableTime."</b></p>");
        $_localTable = array();
        foreach($this->flowsIN as $report){
            if ($report->getLastTimestamp() === $time){
                echo("<p>Router::getTableArray - found valid flow</p>");
                $_localTable[]=$tableTime;
                $_localTable[]=$this->getIpAddress();
                $_localTable[]=$this->getName();
                $_localTable[]=$report->getRouterOutIP();
                $_localTable[]=$report->getRouterOutName();;
                $_localTable[]=$report->getBandwidth(); 
            } 
        }
        //dump($_localTable);
        return $_localTable;
    }
}
