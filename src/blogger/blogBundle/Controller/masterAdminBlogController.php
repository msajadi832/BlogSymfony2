<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\Article;
use blogger\blogBundle\Entity\Comment;
use blogger\blogBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class masterAdminBlogController extends Controller
{
    private $num_list = 5;

    public function dashboardAction()
    {
        return $this->render('bloggerblogBundle:MasterAdminBlog:dashboard.html.twig');
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

        return $this->render('bloggerblogBundle:MasterAdminBlog:showBlogList.html.twig',
            array('blog_list' => $blog_list_base,"pagination" =>array("current" => $start ,"count" => $count)));
    }

    public function blogActiveAction($blog){
        if($blog == "admin")
            return $this->createNotFoundException('وبلاگ مورد نظر پیدا نشد');

        $em = $this->getDoctrine()->getManager();
        $blog_db = $this->getDoctrine()->getRepository('bloggerblogBundle:User')->findOneBy(array('username' => $blog));

        if(!$blog_db)
            return $this->createNotFoundException('وبلاگ مورد نظر پیدا نشد');

        $blog_db->setBlogActive(!$blog_db->getBlogActive());

        $em->persist($blog_db);
        $em->flush();

        return $this->redirect($this->generateUrl('bloggerblog_blog_masterAdmin_blogList'));
    }
}
