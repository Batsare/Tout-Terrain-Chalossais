<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
 */
class Gallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="gallery")
     */
    private $photos;


    public function __construct()
    {
        $this->photos = array();
    }

    public function addPhoto(Photo $file)
    {
        $this->photos[] = $file;

        $file->setGallery($this);

        return $this;
    }

    public function removePhoto(Photo $file)
    {
        $this->photos->removeElement($file);
    }

    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }




}
