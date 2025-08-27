<?php

namespace Hp\Mmbanners\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="mm_mmbanners")
 */

class Banner
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var int
     * @ORM\Column(name="product_id", type="string")
     */

    private $product;


    /**
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;
    /**
     * @var string
     * @ORM\Column(name="short_description", type="string")
     */
    private $shortDescription;

    /**
     * @var string
     * @ORM\Column(name="image", type="string")
     */
    private $image;

    /**
     * @var int
     * @ORM\Column(name="active", type="string")
     */
    private $active;


    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    public function getActive(){

            return $this->active;
        }

        public function setActive($active): self
        {
            $this->active = $active;
            return $this;
        }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product): self
    {
       $this->product = $product;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription( $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    public function setShortDescription( $shortDescription): self
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;
        return $this;
    }



}