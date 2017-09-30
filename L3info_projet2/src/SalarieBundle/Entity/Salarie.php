<?php

namespace SalarieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * salarie
 *
 * @ORM\Table(name="salarie")
 * @ORM\Entity(repositoryClass="\SalarieBundle\Repository\SalarieRepository")
 */
class Salarie
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
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaissance", type="date")
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="sexe", type="string", length=1)
     */
    private $sexe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEntre", type="date")
     */
    private $dateEntre;

    /**
     * @var string
     *
     * @ORM\Column(name="typeContrat", type="string", length=3)
     */
    private $typeContrat;

    /**
     * @var int
     *
     * @ORM\Column(name="DureeContrat", type="integer")
     */
    private $DureeContrat;

    /**
     * @var int
     *
     * @ORM\Column(name="salaire", type="integer")
     */
    private $salaire;

    /**
     * @ORM\ManyToOne(targetEntity="Salarie", cascade={"all"})
     * @ORM\JoinColumn(name="superieurHierarchique", referencedColumnName="id")
     */
    private $superieurHierarchique;

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
     * @return Salarie
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Salarie
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Salarie
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     *
     * @return Salarie
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set dateEntre
     *
     * @param \DateTime $dateEntre
     *
     * @return Salarie
     */
    public function setDateEntre($dateEntre)
    {
        $this->dateEntre = $dateEntre;

        return $this;
    }

    /**
     * Get dateEntre
     *
     * @return \DateTime
     */
    public function getDateEntre()
    {
        return $this->dateEntre;
    }

    /**
     * Set typeContrat
     *
     * @param string $typeContrat
     *
     * @return Salarie
     */
    public function setTypeContrat($typeContrat)
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    /**
     * Get typeContrat
     *
     * @return string
     */
    public function getTypeContrat()
    {
        return $this->typeContrat;
    }

    /**
     * Set dureeContrat
     *
     * @param integer $dureeContrat
     *
     * @return Salarie
     */
    public function setDureeContrat($dureeContrat)
    {
        $this->DureeContrat = $dureeContrat;

        return $this;
    }

    /**
     * Get dureeContrat
     *
     * @return integer
     */
    public function getDureeContrat()
    {
        return $this->DureeContrat;
    }

    /**
     * Set salaire
     *
     * @param integer $salaire
     *
     * @return Salarie
     */
    public function setSalaire($salaire)
    {
        $this->salaire = $salaire;

        return $this;
    }

    /**
     * Get salaire
     *
     * @return integer
     */
    public function getSalaire()
    {
        return $this->salaire;
    }

    /**
     * Set superieurHierarchique
     *
     * @param \SalarieBundle\Entity\Salarie $superieurHierarchique
     *
     * @return Salarie
     */
    public function setSuperieurHierarchique(\SalarieBundle\Entity\Salarie $superieurHierarchique = null)
    {
        $this->superieurHierarchique = $superieurHierarchique;

        return $this;
    }

    /**
     * Get superieurHierarchique
     *
     * @return \SalarieBundle\Entity\Salarie
     */
    public function getSuperieurHierarchique()
    {
        return $this->superieurHierarchique;
    }


}
