<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cotisation
 *
 * @ORM\Table(name="cotisation")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\CotisationRepository")
 */
class Cotisation
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var float
     * @Assert\Range(
     *      min = 80,
     *      max = 100,
     *      minMessage = "Ne peux pas être plus petit que {{ limit }}%",
     *      maxMessage = "Ne peux pas être plus grand que {{ limit }}%"
     * )
     * @ORM\Column(name="salarialeBase", type="float")
     */
    private $salarialeBase;

    /**
     * @var float
     * @Assert\Range(
     *      min = 0,
     *      max = 20,
     *      minMessage = "Ne peux pas être plus petit que {{ limit }}%",
     *      maxMessage = "Ne peux pas être plus grand que {{ limit }}%"
     * )
     * @ORM\Column(name="salarialeTaux", type="float")
     */
    private $salarialeTaux;

    /**
     * @var float
     * @Assert\Range(
     *      min = 80,
     *      max = 100,
     *      minMessage = "Ne peux pas être plus petit que {{ limit }}%",
     *      maxMessage = "Ne peux pas être plus grand que {{ limit }}%"
     * )
     * @ORM\Column(name="patronaleBase", type="float")
     */
    private $patronaleBase;

    /**
     * @var float
     * @Assert\Range(
     *      min = 0,
     *      max = 20,
     *      minMessage = "Ne peux pas être plus petit que {{ limit }}%",
     *      maxMessage = "Ne peux pas être plus grand que {{ limit }}%"
     * )
     * @ORM\Column(name="patronaleTaux", type="float")
     */
    private $patronaleTaux;


    /**
     * @ORM\ManyToOne(targetEntity="\SalarieBundle\Entity\Salarie")
     */
    private $salarie;


    /**
     * @ORM\Column(name="obligatoire", type="boolean")
     */
    private $obligatoire;

    public function __construct()
    {
        $this->patronaleBase= 100.0;
        $this->salarialeBase= 100.0;
        $this->patronaleTaux= 0.0;
        $this->salarialeTaux= 0.0;
        $this->obligatoire= false;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Cotisation
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set salarialeBase
     *
     * @param float $salarialeBase
     *
     * @return Cotisation
     */
    public function setSalarialeBase($salarialeBase)
    {
        $this->salarialeBase = $salarialeBase;

        return $this;
    }

    /**
     * Get salarialeBase
     *
     * @return float
     */
    public function getSalarialeBase()
    {
        return $this->salarialeBase;
    }

    /**
     * Set salarialeTaux
     *
     * @param float $salarialeTaux
     *
     * @return Cotisation
     */
    public function setSalarialeTaux($salarialeTaux)
    {
        $this->salarialeTaux = $salarialeTaux;

        return $this;
    }

    /**
     * Get salarialeTaux
     *
     * @return float
     */
    public function getSalarialeTaux()
    {
        return $this->salarialeTaux;
    }

    /**
     * Set patronaleBase
     *
     * @param float $patronaleBase
     *
     * @return Cotisation
     */
    public function setPatronaleBase($patronaleBase)
    {
        $this->patronaleBase = $patronaleBase;

        return $this;
    }

    /**
     * Get patronaleBase
     *
     * @return float
     */
    public function getPatronaleBase()
    {
        return $this->patronaleBase;
    }

    /**
     * Set patronaleTaux
     *
     * @param float $patronaleTaux
     *
     * @return Cotisation
     */
    public function setPatronaleTaux($patronaleTaux)
    {
        $this->patronaleTaux = $patronaleTaux;

        return $this;
    }

    /**
     * Get patronaleTaux
     *
     * @return float
     */
    public function getPatronaleTaux()
    {
        return $this->patronaleTaux;
    }

    /**
     * Set salarie
     *
     * @param \SalarieBundle\Entity\Salarie $salarie
     *
     * @return Cotisation
     */
    public function setSalarie(\SalarieBundle\Entity\Salarie $salarie = null)
    {
        $this->salarie = $salarie;

        return $this;
    }

    /**
     * Get salarie
     *
     * @return \SalarieBundle\Entity\Salarie
     */
    public function getSalarie()
    {
        return $this->salarie;
    }

    /**
     * Set obligatoire
     *
     * @param boolean $obligatoire
     *
     * @return Cotisation
     */
    public function setObligatoire($obligatoire)
    {
        $this->obligatoire = $obligatoire;

        return $this;
    }

    /**
     * Get obligatoire
     *
     * @return boolean
     */
    public function getObligatoire()
    {
        return $this->obligatoire;
    }
}
