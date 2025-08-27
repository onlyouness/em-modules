<?php

declare(strict_types =1);
namespace Hp\Collectionimages\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table()
 */

class QbCollection
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var int
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="Hp\Collectionimages\Entity\QbCollectionImage", cascade={"persist", "remove"}, mappedBy="collection")
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId() 
    {
        return $this->id;
    }
    public function getUrl(){
        return $this->url;
    }
    public function setUrl (? string $url) : self
    {
        $this->url = $url;
        return $this;
    }
    
    /**
     * @return Collection|QbCollectionImage[]
     */
    public function getImages()
    {
        return $this->images;
    }
    public function addimages(QbCollectionImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setCollection($this); // Set the inverse side of the relation
        }
        return $this;
    }
    public function removeEnrollment(QbCollectionImage $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // Set the owning side to null (unless already changed)
            if ($image->getCollection() === $this) {
                $image->setCollection(null);
            }
        }
        return $this;
    }


}