<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Incidencias_repuestos
 *
 * @ORM\Table(name="incidencias_repuestos")
 * @ORM\Entity(repositoryClass="ScalofrioBundle\Repository\Incidencias_repuestosRepository")
 */
class Incidencias_repuestos
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="incidencias_id", type="integer")
     */
    private $incidenciasId;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="repuestos_id", type="integer")
     */
    private $repuestosId;



    /**
     * Set incidenciasId
     *
     * @param integer $incidenciasId
     *
     * @return Incidencias_repuestos
     */
    public function setIncidenciasId($incidenciasId)
    {
        $this->incidenciasId = $incidenciasId;

        return $this;
    }

    /**
     * Get incidenciasId
     *
     * @return int
     */
    public function getIncidenciasId()
    {
        return $this->incidenciasId;
    }

    /**
     * Set repuestosId
     *
     * @param integer $repuestosId
     *
     * @return Incidencias_repuestos
     */
    public function setRepuestosId($repuestosId)
    {
        $this->repuestosId = $repuestosId;

        return $this;
    }

    /**
     * Get repuestosId
     *
     * @return int
     */
    public function getRepuestosId()
    {
        return $this->repuestosId;
    }
}

