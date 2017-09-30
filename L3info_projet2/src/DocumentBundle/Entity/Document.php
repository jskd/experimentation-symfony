<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="\DocumentBundle\Repository\DocumentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Document extends \DocumentBundle\Entity\Model\Document
{
    public function __construct()
    {
        $this->creation_date = new \DateTime('now');
    }

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
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="proprietaire", referencedColumnName="id")
     */
    private $proprietaire;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="creation_date", type="datetime", length=255)
     */
    private $creation_date;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    protected $path;

    /**
     * @var string
     *
     * @ORM\Column(name="clef", type="string", length=17)
     */
    protected $clef;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    protected $filename;

    /**
     * @Assert\File(maxSize="10000000")
     */
    protected $file;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $publique;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload($directory)
    {
        $clef= bin2hex(openssl_random_pseudo_bytes(8));
        $originalName =  $this->getFile()->getClientOriginalName();
        $path =  $directory . $clef . $originalName ;
        $this->getFile()->move(
            $directory,
            $clef . $originalName
        );

        $this->path = $path;
        $this->filename= $originalName;
        $this->file = null;
        $this->clef= $clef;
    }


    /**
     * Set path
     *
     * @param string $path
     *
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Document
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ( ($this->getPath() . $this->getFilename()) ) {
            unlink($this->getPath());
        }
    }


    /**
     * Set clef
     *
     * @param string $clef
     *
     * @return Document
     */
    public function setClef($clef)
    {
        $this->clef = $clef;

        return $this;
    }

    /**
     * Get clef
     *
     * @return string
     */
    public function getClef()
    {
        return $this->clef;
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
     * Set titre
     *
     * @param string $titre
     *
     * @return Document
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Document
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime  $creationDate
     *
     * @return Document
     */
    public function setCreationDate(\DateTime  $creationDate)
    {
        $this->creation_date = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * Set proprietaire
     *
     * @param \UserBundle\Entity\User $proprietaire
     *
     * @return Document
     */
    public function setProprietaire(\UserBundle\Entity\User $proprietaire = null)
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * Get proprietaire
     *
     * @return \UserBundle\Entity\User
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }


    /**
     * Set publique
     *
     * @param boolean $publique
     *
     * @return Document
     */
    public function setPublique($publique)
    {
        $this->publique = $publique;

        return $this;
    }

    /**
     * Get publique
     *
     * @return boolean
     */
    public function getPublique()
    {
        return $this->publique;
    }
}
