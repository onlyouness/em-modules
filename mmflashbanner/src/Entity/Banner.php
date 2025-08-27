<?php

namespace Hp\MmFlashBanner\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="mm_banners_flash")
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
     * @ORM\Column(name="category_id", type="string")
     */

    private $category;


    /**
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;

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

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category): self
    {
       $this->category = $category;
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