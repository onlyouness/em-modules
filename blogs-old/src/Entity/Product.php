<?php

namespace Hp\Blogs\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**

 * @ORM\Entity
 * @ORM\Table(name="mm_blog_section_products")
 */

class Product

{
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Hp\Blogs\Entity\Section",inversedBy="products")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    private $section;


    
    /**
     *  @ORM\Column(type="integer" ,name="product_id") 
     */
    private $product;


    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }



    public function getSection(): ?Section
    {

        return $this->section;
    }

    public function setSection(?Section $section): self
    {

        $this->section = $section;

        return $this;
    }
    public function getProduct(): ? int
    {

        return $this->product;
    }

    public function setProduct(? int $product): self
    {

        $this->product = $product;

        return $this;
    }


    /**

     * @param mixed $createdAt

     */

    public function setCreatedAt($createdAt): void

    {
        $this->createdAt = $createdAt;
    }



}