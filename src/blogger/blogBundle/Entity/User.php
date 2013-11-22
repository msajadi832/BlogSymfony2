<?php
/**
 * Created by PhpStorm.
 * User: msajadi832
 * Date: 10/30/13
 * Time: 1:01 PM
 */

namespace blogger\blogBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    protected $blogName;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    protected $blogAddress;

    /**
     * @ORM\Column(name="blogDescription", type="text")
     */
    protected $blogDescription;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="user")
     */
    protected $articles;

    public function __construct()
    {
        parent::__construct();
        $this->articles = new ArrayCollection();
        // your own logic
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
     * Set blogName
     *
     * @param string $blogName
     * @return User
     */
    public function setBlogName($blogName)
    {
        $this->blogName = $blogName;
    
        return $this;
    }

    /**
     * Get blogName
     *
     * @return string 
     */
    public function getBlogName()
    {
        return $this->blogName;
    }

    /**
     * Add articles
     *
     * @param \blogger\blogBundle\Entity\Article $articles
     * @return User
     */
    public function addArticle(\blogger\blogBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;
    
        return $this;
    }

    /**
     * Remove articles
     *
     * @param \blogger\blogBundle\Entity\Article $articles
     */
    public function removeArticle(\blogger\blogBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set blogAddress
     *
     * @param string $blogAddress
     * @return User
     */
    public function setBlogAddress($blogAddress)
    {
        $this->blogAddress = $blogAddress;
    
        return $this;
    }

    /**
     * Get blogAddress
     *
     * @return string 
     */
    public function getBlogAddress()
    {
        return $this->blogAddress;
    }

    /**
     * Set blogDescription
     *
     * @param string $blogDescription
     * @return User
     */
    public function setBlogDescription($blogDescription)
    {
        $this->blogDescription = $blogDescription;
    
        return $this;
    }

    /**
     * Get blogDescription
     *
     * @return string 
     */
    public function getBlogDescription()
    {
        return $this->blogDescription;
    }
}