<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Repuestos
 *
 * @ORM\Table(name="repuestos")
 * @ORM\Entity(repositoryClass="ScalofrioBundle\Repository\RepuestosRepository")
 */
class Repuestos
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
     * @ORM\ManyToOne(targetEntity="Maquinas", inversedBy="repuestos")
     * @ORM\JoinColumn(name="maquinas_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $maquinas;


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
     * @return Repuestos
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
     * @return \ScalofrioBundle\Entity\Maquinas
     */
    public function getMaquinas()
    {
        return $this->maquinas;
    }

    /**
     * @param \ScalofrioBundle\Entity\Maquinas $maquinas
     * @return Repuestos
     */
    public function setMaquinas($maquinas)
    {
        $this->maquinas = $maquinas;
    }

    public function __toString() {
        return $this->getNombre();
    }
}

