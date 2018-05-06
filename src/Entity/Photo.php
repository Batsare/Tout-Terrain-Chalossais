<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;



/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Photo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gallery",cascade={"persist"}, inversedBy="photos" )
     * @ORM\JoinColumn(nullable=false)
     */
    private $gallery;

    /**
     * @var UploadedFile
     * @Assert\Image(
     *      mimeTypes = {"image/jpeg","image/jpg"},
     *     mimeTypesMessage="Le format de l'image est incorrect"
     * )
     */
    private $file;

    /**
     * @var ArrayCollection
     */
    private $files;


    // On ajoute cet attribut pour y stocker le nom du fichier temporairement
    private $tempFilename;


    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {

        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (null === $this->file) {
            return;
        }

        // Le nom du fichier est son id, on doit juste stocker également son extension
        // Pour faire propre, on devrait renommer cet attribut en « extension », plutôt que « url »
        $this->url = $this->file->guessExtension();

        // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $this->alt = $this->file->getClientOriginalName();
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (null === $this->file) {
            return;
        }

        // Si on avait un ancien fichier (attribut tempFilename non null), on le supprime
        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
            $this->getUploadRootDir(), // Le répertoire de destination
            $this->id.'.'.$this->url   // Le nom du fichier à créer, ici « id.extension »
        );

        if (!is_dir($this->getUploadSmallDir())) {
            mkdir($this->getUploadSmallDir());
        }
        copy(
            $this->getUploadRootDir().'/'.$this->id.'.'.$this->url,
            $this->getUploadSmallDir().$this->id.'.'.$this->url
        );

        if ( mime_content_type( $this->getUploadSmallDir().$this->id.'.'.$this->url ) === 'image/jpg' || mime_content_type( $this->getUploadSmallDir().$this->id.'.'.$this->url ) === 'image/jpeg'){
            $img = imagecreatefromjpeg($this->getUploadSmallDir().$this->id.'.'.$this->url);
            imagejpeg($img,
                $this->getUploadSmallDir().$this->id.'.'.$this->url,50);
        }
        if ( mime_content_type( $this->getUploadSmallDir().$this->id.'.'.$this->url ) === 'image/png'){
            $img = imagecreatefrompng($this->getUploadSmallDir().$this->id.'.'.$this->url);
            imagepng($img,
                $this->getUploadSmallDir().$this->id.'.'.$this->url,5);
        }
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->url;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
        if (file_exists($this->tempFilename)) {
            // On supprime le fichier
            unlink($this->tempFilename);
        }
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return '/uploads/img/galleries/'.$this->getGallery()->getName();
    }

    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__.'/../../public/'.$this->getUploadDir().'/large/';
    }

    protected function getUploadSmallDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__.'/../../public/'.$this->getUploadDir().'/small/';
    }

    public function getWebPath()
    {
        return '../../../'.$this->getUploadDir().'/large/'.$this->getId().'.'.$this->getUrl();
    }
    public function getWebThumbPath()
    {
        return '../../../'.$this->getUploadDir().'/small/'.$this->getId().'.'.$this->getUrl();
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param mixed $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getTempFilename()
    {
        return $this->tempFilename;
    }

    /**
     * @param mixed $tempFilename
     */
    public function setTempFilename($tempFilename)
    {
        $this->tempFilename = $tempFilename;
    }

    /**
     * @return mixed
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param mixed $gallery
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }






}
