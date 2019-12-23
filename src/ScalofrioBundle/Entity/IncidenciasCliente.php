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
     * @ORM\Column(name="ESTADO", type="integer", length=11)
     */
    private $estado;
    /**
     * @ORM\Column(name="TESTIGO", type="integer", length=11)
     */

    private $testigo;

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
     * Get the value of gestion
     */
    public function getGestion()
    {
        return $this->gestion;
    }

    /**
     * Set the value of gestion
     *
     * @return  self
     */
    public function setGestion($gestion)
    {
        $this->gestion = $gestion;

        return $this;
    }

    /**
     * Get the value of establecimientos
     */
    public function getEstablecimientos()
    {
        return $this->establecimientos;
    }

    /**
     * Set the value of establecimientos
     *
     * @return  self
     */
    public function setEstablecimientos($establecimientos)
    {
        $this->establecimientos = $establecimientos;

        return $this;
    }

    /**
     * Get the value of subestablecimientos
     */
    public function getSubestablecimientos()
    {
        return $this->subestablecimientos;
    }

    /**
     * Set the value of subestablecimientos
     *
     * @return  self
     */
    public function setSubestablecimientos($subestablecimientos)
    {
        $this->subestablecimientos = $subestablecimientos;

        return $this;
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
     * Get the value of estado
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set the value of estado
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


    public function __toString()
    {
        return "";
        // $id = (string)$this->getId();
        // $cli = $this->getUsuario()->getCliente();
        // if($this->getEstablecimientos()!=null) {
        //     $est = $this->getEstablecimientos()->getNombre();
        //     $devolver = $id . ' - ' . $cli . ' - ' . $est;
        // }else{
        //     $devolver = $id . ' - ' . $cli;
        // }

        // return $devolver;
    }
}
