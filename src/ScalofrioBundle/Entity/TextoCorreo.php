<?php

namespace ScalofrioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TextoCorreo
 *
 * @ORM\Table(name="texto_correo")
 * @ORM\Entity(repositoryClass="ScalofrioBundle\Repository\TextoCorreoRepository")
 */
class TextoCorreo
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
     * @ORM\Column(name="superior", type="text")
     */
    private $superior;

    /**
     * @var string
     *
     * @ORM\Column(name="inferior", type="text")
     */
    private $inferior;


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
     * Set superior
     *
     * @param string $superior
     *
     * @return TextoCorreo
     */
    public function setSuperior($superior)
    {
        $this->superior = $superior;

        return $this;
    }

    /**
     * Get superior
     *
     * @return string
     */
    public function getSuperior()
    {
        return $this->superior;
    }

    /**
     * Set inferior
     *
     * @param string $inferior
     *
     * @return TextoCorreo
     */
    public function setInferior($inferior)
    {
        $this->inferior = $inferior;

        return $this;
    }

    /**
     * Get inferior
     *
     * @return string
     */
    public function getInferior()
    {
        return $this->inferior;
    }
}

