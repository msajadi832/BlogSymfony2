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
        $this->get('session')->getFlashBag()->add('adminSuccess', 'وضعبت وبلاگ '.$blog_db->getBlogName().' با موفقیت تغییر یافت.');
        return $this->redirect($this->generateUrl('bloggerblog_blog_masterAdmin_blogList'));
    }

    public function removeBlogAction($blog){
        if($blog == "admin")
            return $this->createNotFoundException('وبلاگ مورد نظر پیدا نشد');

        $em = $this->getDoctrine()->getManager();
        $blog_db = $this->getDoctrine()->getRepository('bloggerblogBundle:User')->findOneBy(array('username' => $blog));

        if(!$blog_db)
            return $this->createNotFoundException('وبلاگ مورد نظر پیدا نشد');


        $em->remove($blog_db);
        $em->flush();
        $this->get('session')->getFlashBag()->add('adminSuccess', 'وبلاگ '.$blog_db->getBlogName().' با موفقیت حذف شد.');
        return $this->redirect($this->generateUrl('bloggerblog_blog_masterAdmin_blogList'));
    }

    public function editDefaultTemplateAction(Request $request)
    {
        $user = $this->getUser();
        $template_form = $this->createFormBuilder($user)
            ->add('blogTemplate','textarea',array('label'  => 'قالب وبلاگ', 'attr' => array('style' => 'direction:ltr;text-align:justify;width:100%;')))
            ->add('submit', 'submit', array('label'  => 'ویرایش', 'attr' => array("class" => "btn")))
            ->getForm();
        $template_form->handleRequest($request);
        if ($template_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('adminSuccess', 'قالب پیشفرض با موفقیت ویرایش شد.');
        }
        return $this->render('bloggerblogBundle:MasterAdminBlog:editTemplate.html.twig',
            array("template_form" => $template_form->createView()));
    }

    public function editHomePageContentAction(Request $request)
    {
        $user = $this->getUser();
        $template_form = $this->createFormBuilder($user)
            ->add('blogDescription','textarea',array('label'  => 'محتوای صفحه اصلی', 'attr' => array('class' => "ckeditor",'style' => 'width:100%')))
            ->add('submit', 'submit', array('label'  => 'ویرایش', 'attr' => array("class" => "btn")))
            ->getForm();
        $template_form->handleRequest($request);
        if ($template_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('adminSuccess', 'محتوای صفحه اصلی با موفقیت ویرایش شد.');
        }
        return $this->render('bloggerblogBundle:MasterAdminBlog:editHomePageContent.html.twig',
            array("homePage_form" => $template_form->createView()));
    }
}
