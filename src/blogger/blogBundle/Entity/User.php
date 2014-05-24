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
     * @ORM\Column(type="string", length=100)
     */
    protected $blogName;

    /**
     * @ORM\Column(type="text")
     */
    protected $blogDescription;

    /**
     * @ORM\Column(type="text")
     */
    protected $blogTemplate;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $family;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="user" ,cascade={"all"})
     */
    protected $articles;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user")
     */
    protected $comments;

    public function __construct()
    {
        parent::__construct();
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = array('ROLE_USER');
        $this->blogTemplate = "dgfg";
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

    /**
     * Set blogTemplate
     *
     * @param string $blogTemplate
     * @return User
     */
    public function setBlogTemplate($blogTemplate)
    {
        $this->blogTemplate = $blogTemplate;

        return $this;
    }

    /**
     * Get blogTemplate
     *
     * @return string
     */
    public function getBlogTemplate()
    {
        return $this->blogTemplate;
    }

    /**
     * Add comments
     *
     * @param \blogger\blogBundle\Entity\Comment $comments
     * @return User
     */
    public function addComment(\blogger\blogBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \blogger\blogBundle\Entity\Comment $comments
     */
    public function removeComment(\blogger\blogBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set family
     *
     * @param string $family
     * @return User
     */
    public function setFamily($family)
    {
        $this->family = $family;
    
        return $this;
    }

    /**
     * Get family
     *
     * @return string 
     */
    public function getFamily()
    {
        return $this->family;
    }
}