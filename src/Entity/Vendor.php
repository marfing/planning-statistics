<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VendorRepository")
 */
class Vendor
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contact;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NetworkElement", mappedBy="vendor")
     */
    private $networkElements;

    public function __construct()
    {
        $this->networkElements = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Collection|NetworkElement[]
     */
    public function getNetworkElements(): Collection
    {
        return $this->networkElements;
    }

    public function addNetworkElement(NetworkElement $networkElement): self
    {
        if (!$this->networkElements->contains($networkElement)) {
            $this->networkElements[] = $networkElement;
            $networkElement->setVendor($this);
        }

        return $this;
    }

    public function removeNetworkElement(NetworkElement $networkElement): self
    {
        if ($this->networkElements->contains($networkElement)) {
            $this->networkElements->removeElement($networkElement);
            // set the owning side to null (unless already changed)
            if ($networkElement->getVendor() === $this) {
                $networkElement->setVendor(null);
            }
        }

        return $this;
    }
}
