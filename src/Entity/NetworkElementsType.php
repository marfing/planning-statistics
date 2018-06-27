<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NetworkElementsTypeRepository")
 */
class NetworkElementsType
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
     * @ORM\OneToMany(targetEntity="App\Entity\NetworkElement", mappedBy="networkElementsType")
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
            $networkElement->setNetworkElementsType($this);
        }

        return $this;
    }

    public function removeNetworkElement(NetworkElement $networkElement): self
    {
        if ($this->networkElements->contains($networkElement)) {
            $this->networkElements->removeElement($networkElement);
            // set the owning side to null (unless already changed)
            if ($networkElement->getNetworkElementsType() === $this) {
                $networkElement->setNetworkElementsType(null);
            }
        }

        return $this;
    }
}
