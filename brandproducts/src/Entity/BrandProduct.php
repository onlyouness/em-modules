<?php

declare(strict_types =1);
namespace Hp\Brandproducts\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Hp\Brandproducts\Repository\BrandProductRepository")
 * @ORM\HasLifecycleCallbacks
 */

class BrandProduct
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
     * @ORM\Column(name="brand_id", type="integer",nullable=true)
     */
    private $brand;
    /**
     * @var int
     * @ORM\Column(name="category_id", type="integer",nullable=true)
     */
    private $category;
    /**
     * @var int
     * @ORM\Column(name="display_type", type="string")
     */
    private $type;
    /**
     * @ORM\Column(name="created_at",type="datetime")
     */
    private \DateTime $createdAt;
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    public function getId()
    {
        return $this->id;
    }
    public function getBrand(){
        return $this->brand;
    }
    public function setBrand( $brand) : self
    {
        $this->brand = $brand;
        return $this;
    }
    public function getCategory(){
        return $this->category;
    }
    public function setCategory( $category) : self
    {
        $this->category = $category;
        return $this;
    }
    public function getType(){
        return $this->type;
    }
    public function setType($type) : self
    {
        $this->type = $type;
        return $this;
    }
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'brand' => $this->getBrand(),
            'category' => $this->getCategory(),
            'displayType' => $this->getType(),
        ];
    }

}