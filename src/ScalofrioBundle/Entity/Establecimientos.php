<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Establecimientos
 *
 * @ORM\Table(name="establecimientos")
 * @ORM\Entity(repositoryClass="ScalofrioBundle\Repository\EstablecimientosRepository")
 */
class Establecimientos
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
     * @ORM\Column(name="Nombre", type="string", length=255)
     */
    private $nombre;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="establecimientos")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $cliente;


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
     * @return Establecimientos
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
     * @return Establecimientos
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }


    public function __toString() {
        return $this->getNombre();
    }
}

