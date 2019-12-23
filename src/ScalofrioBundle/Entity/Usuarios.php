<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Usuarios
 *
 * @ORM\Table(name="usuarios")
 * @ORM\Entity(repositoryClass="ScalofrioBundle\Repository\UsuariosRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Usuarios implements AdvancedUserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100)
     */
    private $username;


    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", columnDefinition="ENUM('ROLE_ADMIN','ROLE_COMERCIAL','ROLE_USER')", length=50)
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"ROLE_ADMIN","ROLE_COMERCIAL","ROLE_USER"})
     */
    private $role;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

     /**
      *
      * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="usuarios")
      * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", onDelete="CASCADE")
      */

    protected $cliente;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Establecimientos", inversedBy="usuarios")
     * @ORM\JoinColumn(name="establecimientos_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $establecimientos;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Comercial", inversedBy="usuarios")
     * @ORM\JoinColumn(name="comercial_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $comercial;


    public function __construct()
    {
        $this->isActive = true;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Usuarios
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuarios
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Usuarios
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
 * Set isActive
 *
 * @param boolean $isActive
 *
 * @return Usuarios
 */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Usuarios
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Usuarios
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getRoles()
    {
        return array($this->role);
    }

    public function getSalt()
    {
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
            $this->isActive
        ));
    }
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
            ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }
    public function isAccountNonLocked()
    {
        return true;
    }
    public function isCredentialsNonExpired()
    {
        return true;
    }
    public function isEnabled()
    {
        return $this->isActive;
    }

     /**
      * @return \ScalofrioBundle\Entity\Cliente
      */
     public function getCliente()
     {
         return $this->cliente;
     }

     /**
      * @param \ScalofrioBundle\Entity\Cliente $cliente
      * @return Usuarios
      */
     public function setCliente($cliente)
     {
    $this->cliente = $cliente;
     }

    /**
     * @return \ScalofrioBundle\Entity\Establecimientos
     */
    public function getEstablecimientos()
    {
        return $this->establecimientos;
    }

    /**
     * @param \ScalofrioBundle\Entity\Establecimientos $establecimientos
     * @return Usuarios
     */
    public function setEstablecimientos($establecimientos)
    {
        $this->establecimientos = $establecimientos;
    }

    /**
     * @return \ScalofrioBundle\Entity\Comercial
     */
    public function getComercial()
    {
        return $this->comercial;
    }

    /**
     * @param \ScalofrioBundle\Entity\Comercial $comercial
     * @return Usuarios
     */
    public function setComercial($comercial)
    {
        $this->comercial = $comercial;
    }


    public function __toString()
    {
        return $this->getUsername();
    }
}

