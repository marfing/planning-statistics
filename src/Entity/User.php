<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FeasibilityB2B", mappedBy="User")
     */
    private $feasibilitiesB2B;

    public function __construct()
    {
        $this->feasibilitiesB2B = new ArrayCollection();
    }


    public function _construct(){
        $this->isActive = true;
        $this->roles = array('ROLE_USER');

    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRoles(){
        return $this->roles;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role){
        $this->roles[] = $role;
    }

    /**
     * @return Collection|FeasibilityB2B[]
     */
    public function getFeasibilitiesB2B(): Collection
    {
        return $this->feasibilitiesB2B;
    }

    public function addFeasibilitiesB2B(FeasibilityB2B $feasibilitiesB2B): self
    {
        if (!$this->feasibilitiesB2B->contains($feasibilitiesB2B)) {
            $this->feasibilitiesB2B[] = $feasibilitiesB2B;
            $feasibilitiesB2B->setUser($this);
        }

        return $this;
    }

    public function removeFeasibilitiesB2B(FeasibilityB2B $feasibilitiesB2B): self
    {
        if ($this->feasibilitiesB2B->contains($feasibilitiesB2B)) {
            $this->feasibilitiesB2B->removeElement($feasibilitiesB2B);
            // set the owning side to null (unless already changed)
            if ($feasibilitiesB2B->getUser() === $this) {
                $feasibilitiesB2B->setUser(null);
            }
        }

        return $this;
    }

    public function isAdmin(){
        foreach ($this->roles as $role ){
            if ($role == "ROLE_ADMIN") return true;
        }
        return false;
    }
}
