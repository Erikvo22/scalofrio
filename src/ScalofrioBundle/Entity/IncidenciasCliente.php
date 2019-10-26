<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Table(name="INCIDENCIAS_CLIENTES")
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
     * @Assert\NotBlank()
     * @ORM\Column(name="TITULO", type="string", length=80)
     */
    private $titulo;

    /**
     * @ORM\Column(name="TIPO", type="string", length=50)
     */
    private $tipo;

    /**
     * @Assert\Choice(choices = {"BAJA", "MEDIA", "ALTA","URGENTE"})
     * @ORM\Column(name="PRIORIDAD", type="string", length=25)
     */
    private $prioridad;

    /**
     * @ORM\Column(name="FECHA_INCIDENCIA", type="datetime")
     */
    private $fechaIncidencia;

    /**
     * @ORM\Column(name="DESCRIPCION", type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\Column(name="FECHA_CREACION", type="datetime")
     */
    private $fechaCreacion;

    /**
     * @Assert\NotNull(
     *      message = "Debe elegir a un cliente"
     * )
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="incidenciasCliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $cliente;

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
     * Get the value of tipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set the value of tipo
     *
     * @return  self
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get the value of prioridad
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Set the value of prioridad
     *
     * @return  self
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

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
     * Get the value of fechaCreacion
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set the value of fechaCreacion
     *
     * @return  self
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
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
     * @return Incidencias
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Get the value of titulo
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set the value of titulo
     *
     * @return  self
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }
}
