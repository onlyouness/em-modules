<?php



namespace Hp\Blogs\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Adapter\Entity\Category;


/**
 * @ORM\Entity
 * @ORM\Table(name="mm_blog_sections")
 */


class Section

{

    /**

     * @ORM\Id

     * @ORM\GeneratedValue

     * @ORM\Column(type="integer")

     */

    private int $id;

    /**
     *   @ORM\ManyToOne(targetEntity= "Hp\Blogs\Entity\Blog", inversedBy = "sections")
     *   @ORM\JoinColumn(name= "blog_id", referencedColumnName= "id",onDelete="CASCADE")
     *   
     */

    private ?Blog $blog = null;



    /**
     *  @ORM\Column(type="string" ,name="title") 
     */

    private $title;


    /**
     *  @ORM\Column(type="string" ,name="products") 
     */
    private $products ;

    /**

     * @ORM\Column(type="datetime")

     */

    private \DateTime $createdAt;

    /**
     * @ORM\Column(type="integer",name="position")
     */
    private $position;

    public function __construct()

    {

        $this->createdAt = new \DateTime();


    }



    public function getId()
    {

        return $this->id;
    }



    /**

     * @return mixed

     */

    public function getTitle()

    {

        return $this->title;
    }



    /**

     * @param mixed $title

     */

    public function setTitle($title): void

    {

        $this->title = $title;
    }



    /**

     * @return mixed

     */

    public function getProducts()

    {

        return $this->products;
    }



    /**

     * @param mixed $products

     */

    public function setProducts($products): void

    {

        $this->products = $products;
    }



    /**

     * @return mixed

     */

    public function getCreatedAt()

    {

        return $this->createdAt;
    }
    public function getPosition(){
        return $this->position;
    }
    public function setPosition($position):self{
        $this->position = $position;
        return $this;
    }


    /**

     * @param mixed $createdAt

     */

    public function setCreatedAt($createdAt): void

    {

        $this->createdAt = $createdAt;
    }



    public function getBlog(): ?Blog
    {

        return $this->blog;
    }

    public function setBlog(?Blog $blog): self
    {

        $this->blog = $blog;

        return $this;
    }
}
