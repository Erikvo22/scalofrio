<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Incidencias
 *
 * @ORM\Table(name="incidencias")
 * @ORM\Entity(repositoryClass="ScalofrioBundle\Repository\IncidenciasRepository")
 */
class Incidencias
{
    /**
     *
     * @ORM\ManyToOne(targetEntity="Ruta", inversedBy="incidencias")
     * @ORM\JoinColumn(name="ruta_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $ruta;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Comercial", inversedBy="incidencias")
     * @ORM\JoinColumn(name="comercial_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $comercial;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="incidencias")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $cliente;


    /**
     *
     * @ORM\ManyToOne(targetEntity="Gestion", inversedBy="incidencias")
     * @ORM\JoinColumn(name="gestion_id", referencedColumnName="id", onDelete="CASCADE")
     */

    protected $gestion;


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="tiempo", type="integer")
     */
    private $tiempo;

    /**
     * @var string
     *
     * @ORM\Column(name="establecimiento", type="string", length=255, nullable=true)
     */
    private $establecimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="cargocliente", type="string", length=100)
     */
    private $cargocliente;

    /**
     * @var string
     *
     * @ORM\Column(name="nombrecliente", type="string", length=255)
     */
    private $nombrecliente;

    /**
     * @var string
     *
     * @ORM\Column(name="firma", type="string", length=255, nullable=true)
     */
    private $firma;

    /**
     * @var string
     *
     * @ORM\Column(name="resultado", type="string", length=255)
     */
    private $resultado;

    /**
     * @var string
     *
     * @ORM\Column(name="repuestos", type="string", length=255)
     */
    private $repuestos;



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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Incidencias
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set tiempo
     *
     * @param integer $tiempo
     *
     * @return Incidencias
     */
    public function setTiempo($tiempo)
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    /**
     * Get tiempo
     *
     * @return int
     */
    public function getTiempo()
    {
        return $this->tiempo;
    }

    /**
     * Set cargocliente
     *
     * @param string $cargocliente
     *
     * @return Incidencias
     */
    public function setCargocliente($cargocliente)
    {
        $this->cargocliente = $cargocliente;

        return $this;
    }

    /**
     * Get cargocliente
     *
     * @return string
     */
    public function getCargocliente()
    {
        return $this->cargocliente;
    }


    /**
     * Set establecimiento
     *
     * @param string $establecimiento
     *
     * @return Incidencias
     */
    public function setEstablecimiento($establecimiento)
    {
        $this->establecimiento = $establecimiento;

        return $this;
    }

    /**
     * Get establecimiento
     *
     * @return string
     */
    public function getEstablecimiento()
    {
        return $this->establecimiento;
    }


    /**
     * Set nombrecliente
     *
     * @param string $nombrecliente
     *
     * @return Incidencias
     */
    public function setNombrecliente($nombrecliente)
    {
        $this->nombrecliente = $nombrecliente;

        return $this;
    }

    /**
     * Get nombrecliente
     *
     * @return string
     */
    public function getNombrecliente()
    {
        return $this->nombrecliente;
    }

    /**
     * Set firma
     *
     * @param string $firma
     *
     * @return Incidencias
     */
    public function setFirma($firma)
    {
        $this->firma = $firma;

        return $this;
    }

    /**
     * Get firma
     *
     * @return string
     */
    public function getFirma()
    {
        return $this->firma;
    }

    /**
     * Set resultado
     *
     * @param string $resultado
     *
     * @return Incidencias
     */
    public function setResultado($resultado)
    {
        $this->resultado = $resultado;

        return $this;
    }

    /**
     * Get resultado
     *
     * @return string
     */
    public function getResultado()
    {
        return $this->resultado;
    }

    /**
     * Set repuestos
     *
     * @param string $repuestos
     *
     * @return Incidencias
     */
    public function setRepuestos($repuestos)
    {
        $this->repuestos = $repuestos;

        return $this;
    }

    /**
     * Get repuestos
     *
     * @return string
     */
    public function getRepuestos()
    {
        return $this->repuestos;
    }

    /**
     * @return \ScalofrioBundle\Entity\Ruta
*/
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * @param \ScalofrioBundle\Entity\Ruta $ruta
     * @return Incidencias
     */
    public function setRuta($ruta)
    {
        $this->ruta = $ruta;
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
     * @return Incidencias
     */
    public function setComercial($comercial)
    {
        $this->comercial = $comercial;
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
     * @return \ScalofrioBundle\Entity\Gestion
     */
    public function getGestion()
    {
        return $this->gestion;
    }

    /**
     * @param \ScalofrioBundle\Entity\Gestion $gestion
     * @return Incidencias
     */
    public function setGestion($gestion)
    {
        $this->gestion = $gestion;
    }


}

