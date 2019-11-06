<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subestablecimientos
 *
 * @ORM\Table(name="subestablecimientos")
 * @ORM\Entity(repositoryClass="ScalofrioBundle\Repository\SubestablecimientosRepository")
 */
class Subestablecimientos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="subestablecimientos")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     */
    protected $cliente;

    /**
     * @ORM\ManyToOne(targetEntity="Establecimientos", inversedBy="subestablecimientos")
     * @ORM\JoinColumn(name="establecimientos_id", referencedColumnName="id")
     */
    protected $establecimientos;


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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Subestablecimientos
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
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
     * @return Subestablecimientos
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
     * @return Subestablecimientos
     */
    public function setEstablecimientos($establecimientos)
    {
        $this->establecimientos = $establecimientos;
    }

    public function __toString() {
        return $this->getNombre();
    }
}

