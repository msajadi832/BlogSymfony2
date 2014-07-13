<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    private $num_list = 20;

    public function indexAction()
    {
        $blog_db = $this->getDoctrine()->getRepository('bloggerblogBundle:User')->findOneBy(array('username' => 'admin'));

        if(!$blog_db)
            return $this->createNotFoundException('وبلاگ مورد نظر پیدا نشد');
        return $this->render('bloggerblogBundle:Default:index.html.twig', array('blog' => $blog_db));
    }

    public function loginAction()
    {
        return $this->render('bloggerblogBundle:Default:login.html.twig');
    }

    public function showBlogListAction($start){
        $em = $this->getDoctrine()->getManager();
        $blog_list_base = $em->createQueryBuilder()
            ->select('user')
            ->from('bloggerblogBundle:User','user')
            ->where('user.username <> :admin')
            ->setParameter('admin','admin')
            ->orderBy('user.id','DESC')
            ->setFirstResult(($start-1)*$this->num_list)
            ->setMaxResults($this->num_list)
            ->getQuery()->getResult();

        $count = ceil((count($this->getDoctrine()->getRepository('bloggerblogBundle:User')->findAll())-1)/$this->num_list);

//        $blog_list_base = $this->getDoctrine()->getRepository('bloggerblogBundle:User')->findBy(array(),array("id" => 'DESC'),$this->num_list,($start-1)*$this->num_list);
        $blog_list = array();
        /** @var $singleBlog User */
        foreach($blog_list_base as $singleBlog){
            $blog_list[] = array("blog_address" => $singleBlog->getUsername(),"blog_name" =>$singleBlog->getBlogName(),
                "blog_description" => $singleBlog->getBlogDescription());
        }
        return $this->render('bloggerblogBundle:Default:showBlogList.html.twig',
            array('blog_list' => $blog_list,"pagination" =>array("current" => $start ,"count" => $count)));
    }
}
