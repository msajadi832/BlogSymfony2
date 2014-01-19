<?php

namespace blogger\blogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    private $num_list = 2;

    public function indexAction()
    {
        return $this->render('bloggerblogBundle:Default:index.html.twig');
    }

    public function showBlogListAction($start){
        $count = ceil(count($this->getDoctrine()->getRepository('bloggerblogBundle:User')->findAll())/$this->num_list);

        $blog_list_base = $this->getDoctrine()->getRepository('bloggerblogBundle:User')->findBy(array(),array("id" => 'DESC'),$this->num_list,($start-1)*$this->num_list);
        $blog_list = array();
        foreach($blog_list_base as $singleBlog){
            $blog_list[] = array("blog_address" => $singleBlog->getBlogAddress(),"blog_name" =>$singleBlog->getBlogName(),
                "blog_description" => $singleBlog->getBlogDescription());
        }
        return $this->render('bloggerblogBundle:Default:showBlogList.html.twig',
            array('blog_list' => $blog_list,"pagination" =>array("current" => $start ,"count" => $count)));
    }
}
