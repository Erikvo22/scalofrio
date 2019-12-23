<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="incidencias_clientes")
 * @ORM\Entity()
 */
class IncidenciasCliente
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Gestion", inversedBy="incidenciasCliente")
     * @ORM\JoinColumn(name="gestion_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $gestion;

    /**
     * @ORM\ManyToOne(targetEntity="Establecimientos", inversedBy="incidenciasCliente")
     * @ORM\JoinColumn(name="establecimientos_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $establecimientos;

    /**
     * @ORM\ManyToOne(targetEntity="Subestablecimientos", inversedBy="incidenciasCliente")
     * @ORM\JoinColumn(name="subestablecimientos_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $subestablecimientos;

    /**
     * @ORM\Column(name="FECHA_INCIDENCIA", type="datetime")
     */
    private $fechaIncidencia;

    /**
     * @ORM\Column(name="DESCRIPCION", type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\Column(name="estado", type="integer", length=1)
     */
    private $estado = 0;

    /**
     * @ORM\Column(name="testigo", type="integer", length=1)
     */
    private $testigo = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of descripcion
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     *
     * @return  self
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return \ScalofrioBundle\Entity\Gestion
     */
    public function getGestion()
    {
        return $this->gestion;
    }

    /**
     * @param \ScalofrioBundle\Entity\Gestion $gestion
     * @return IncidenciasCliente
     */
    public function setGestion($gestion)
    {
        $this->gestion = $gestion;
    }


    /**
     * Get the value of fechaIncidencia
     */
    public function getFechaIncidencia()
    {
        return $this->fechaIncidencia;
    }

    /**
     * Set the value of fechaIncidencia
     *
     * @return  self
     */
    public function setFechaIncidencia($fechaIncidencia)
    {
        $this->fechaIncidencia = $fechaIncidencia;

        return $this;
    }

    /**
     * Get the value of titulo
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set the value of titulo
     *
     * @return  self
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get the value of testigo
     */
    public function getTestigo()
    {
        return $this->testigo;
    }

    /**
     * Set the value of testigo
     *
     * @return  self
     */
    public function setTestigo($testigo)
    {
        $this->testigo = $testigo;

        return $this;
    }

    /**
     * Get the value of usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set the value of usuario
     *
     * @return  self
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return \ScalofrioBundle\Entity\Subestablecimientos
     */
    public function getSubestablecimientos()
    {
        return $this->subestablecimientos;
    }

    /**
     * @param \ScalofrioBundle\Entity\Subestablecimientos $subestablecimientos
     * @return IncidenciasCliente
     */
    public function setSubestablecimientos($subestablecimientos)
    {
        $this->subestablecimientos = $subestablecimientos;
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
     * @return IncidenciasCliente
     */
    public function setEstablecimientos($establecimientos)
    {
        $this->establecimientos = $establecimientos;
    }

    // public function __toString()
    // {
    //     $id = (string)$this->getId();
    //     $cli = $this->getUsuario()->getCliente();
    //     if($this->getEstablecimientos()!=null) {
    //         $est = $this->getEstablecimientos()->getNombre();
    //         $devolver = $id . ' - ' . $cli . ' - ' . $est;
    //     }else{
    //         $devolver = $id . ' - ' . $cli;
    //     }

    //     return $devolver;
    // }
}
