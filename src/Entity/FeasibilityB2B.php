<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use \Datetime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FeasibilityB2BRepository")
 */
class FeasibilityB2B
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
    private $CustomerName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $TrunkType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $IPType;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $CodecList;

    /**
     * @ORM\Column(type="array")
     */
    private $Customer2TiscaliCapacity = [
        'Channels' => 0,
        'MinutesPerMonth' => 0,
        'Erlang' => 0
    ];

    /**
     * @ORM\Column(type="array")
     */
    private $Tiscali2CustomerCapacity= [
        'Channels' => 0,
        'MinutesPerMonth' => 0,
        'Erlang' => 0
    ];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="feasibilitiesB2B")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\Column(type="date")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $rejectDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $approveDate;

    

    public function getId()
    {
        return $this->id;
    }

    public function getCustomerName(): ?string
    {
        return $this->CustomerName;
    }

    public function setCustomerName(string $CustomerName): self
    {
        $this->CustomerName = $CustomerName;

        return $this;
    }

    public function getTrunkType(): ?string
    {
        return $this->TrunkType;
    }

    public function setTrunkType(string $TrunkType): self
    {
        $this->TrunkType = $TrunkType;

        return $this;
    }

    public function getIPType(): ?string
    {
        return $this->IPType;
    }

    public function setIPType(string $IPType): self
    {
        $this->IPType = $IPType;

        return $this;
    }

    public function getCodecList(): ?array
    {
        return $this->CodecList;
    }

    public function setCodecList(array $CodecList): self
    {
        $this->CodecList = $CodecList;

        return $this;
    }

    public function getCustomer2TiscaliCapacity(): ?array
    {
        return $this->Customer2TiscaliCapacity;
    }

    public function setCustomer2TiscaliCapacity(array $Customer2TiscaliCapacity): self
    {
        $this->Customer2TiscaliCapacity = $Customer2TiscaliCapacity;

        return $this;
    }

    public function getTiscali2CustomerCapacity(): ?array
    {
        return $this->Tiscali2CustomerCapacity;
    }

    public function setTiscali2CustomerCapacity(array $Tiscali2CustomerCapacity): self
    {
        $this->Tiscali2CustomerCapacity = $Tiscali2CustomerCapacity;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(?string $Status): self
    {
        $this->Status = $Status;
        $date = new DateTime('now');
        switch($Status){
            case "new":
                $this->SetCreationDate($date);
            break;
            case "approved":
                $this->SetApproveDate($date);
            break;
            case "rejected":
                $this->SetRejectDate($date);
            break;
        }

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->Note;
    }

    public function setNote(?string $Note): self
    {
        $this->Note = $Note;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getRejectDate(): ?\DateTimeInterface
    {
        return $this->rejectDate;
    }

    public function setRejectDate(?\DateTimeInterface $rejectDate): self
    {
        $this->rejectDate = $rejectDate;

        return $this;
    }

    public function getApproveDate(): ?\DateTimeInterface
    {
        return $this->approveDate;
    }

    public function setApproveDate(?\DateTimeInterface $approveDate): self
    {
        $this->approveDate = $approveDate;

        return $this;
    }
}
