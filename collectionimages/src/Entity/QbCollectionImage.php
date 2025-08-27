<?php

declare(strict_types =1);
namespace Hp\Collectionimages\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table()
 */

class QbCollectionImage
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(name="image_path", type="string")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Hp\Collectionimages\Entity\QbCollection",inversedBy="images")
     * @ORM\JoinColumn(name="id_collection", referencedColumnName="id")
     */
    private $collection;

    public function getId() 
    {
        return $this->id;
    }
    public function getImage(){
        return $this->image;
    }
    public function setImage( $image) : self
    {
        $this->image = $image;
        return $this;
    }

    public function getCollection(): ?QbCollection
    {
        return $this->collection;
    }
    public function setCollection(?QbCollection $collection): self
    {
        $this->collection = $collection;
        return $this;
    }


}