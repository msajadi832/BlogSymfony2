<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\Article;
use blogger\blogBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class adminBlogController extends Controller
{

    public function dashboardAction()
    {
        $user = $this->getUser();
        $commentsRes = $this->getDoctrine() ->getRepository('bloggerblogBundle:Comment') ->findBy(array("user" => $user->getId()),array("date" => 'DESC',"id" => 'DESC'));
        $comments = array();
        foreach($commentsRes as $singleComment){
            $article_comment = $singleComment->getArticle();
//            $article_comment = new Article();
            $comments[] = array("id" =>$singleComment->getId(),
                "name" => $singleComment->getName(),
                "date" => date_format($singleComment->getDate(),"Y-m-d"),
                "confirmed" => $singleComment->getConfirmed(),
                "articleTitle" => $article_comment->getTitle(),
                "articleAddress" => $article_comment->getAddress());
        }
        return $this->render('bloggerblogBundle:AdminBlog:dashboard.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
            'comments' => $comments));

    }

    public function addArticleAction(Request $request)
    {
        $user = $this->getUser();

        $article = new Article();
        $article->setUser($user);

        $article_form = $this->createFormBuilder($article)
            ->add('title','text',array('label'  => 'عنوان', 'attr' => array('style' => 'height:25px')))
            ->add('body','textarea',array('label'  => 'بدنه', 'attr' => array('class' => "ckeditor",'style' => 'width:100%')))
            ->add('publishDate','date',array('data' => new \DateTime(),'label'  => 'تاریخ انتشار', 'attr' => array('style' => 'height:25px;margin-bottom:10px;')))
            ->add('submit', 'submit', array('label'  => 'ثبت مطلب'))
            ->getForm();
        $article_form->handleRequest($request);
        if ($article_form->isValid()) {
            $article->setAddress($user->getId().time().mt_rand(0,100));
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->get('session')->getFlashBag()->add('adminSuccess', 'مطلب '.$article->getTitle().' با موفقیت ثبت شد.');
        }
        return $this->render('bloggerblogBundle:AdminBlog:addArticle.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "article_form" => $article_form->createView()));
    }

    public function showRecentArticlesAction(){
        $user = $this->getUser();
        $article = $this->getDoctrine()->getRepository('bloggerblogBundle:Article');
        $qb = $article->createQueryBuilder('a')
            ->where("a.user = :user")
            ->setParameter("user",$user->getId())
            ->orderBy('a.id', 'DESC')
            ->getQuery();
        $articlesa =$qb->getResult();
        $articles = array();
        foreach($articlesa as $singleArticle){
            $articles[] = array("article_id" => $singleArticle->getId(),"article_title" => $singleArticle->getTitle(),"article_address" => $singleArticle->getAddress(),
                "article_date" => $singleArticle->getPublishDate(), "article_comment_count" => $singleArticle->getComments()->count());
        }
        return $this->render('bloggerblogBundle:AdminBlog:showRecentArticles.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),"articles" => $articles));

    }
    public function removeArticleAction($address){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('bloggerblogBundle:Article')->findOneBy(array("user" => $user, "address" => $address));
        if (!$article) {
            throw $this->createNotFoundException(
                "خطا: این مقاله  پیدا نشد<br />"
            );
        }

        $em->remove($article);
        $em->flush();
        $this->get('session')->getFlashBag()->add('adminSuccess', 'مطلب '.$article->getTitle().' با موفقیت حذف شد.');
        return $this->redirect($this->generateUrl('bloggerblog_blogAdminShowRecentArticles'));
    }
    public function removeCommentAction($id,$articleId){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('bloggerblogBundle:Comment')->findOneBy(array("user" => $user, "id" => $id));
        if (!$comment) {
            throw $this->createNotFoundException(
                "خطا: این نظر پیدا نشد<br />"
            );
        }

        $em->remove($comment);
        $em->flush();
        $this->get('session')->getFlashBag()->add('adminSuccess', 'نظر '.$comment->getName().' با موفقیت حذف شد.');
        return $this->redirect($this->generateUrl('bloggerblog_blogAdminShowRecentComments',array('articleId' => $articleId)));
    }

    public function editArticleAction($address,Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('bloggerblogBundle:Article')->findOneBy(array("user" => $user, "address" => $address));
        if (!$article) {
            throw $this->createNotFoundException(
                "خطا: این مقاله پیدا نشد<br />"
            );
        }

        $article_form = $this->createFormBuilder($article)
            ->add('title','text',array('label'  => 'عنوان', 'attr' => array('style' => 'height:25px')))
            ->add('body','textarea',array('label'  => 'بدنه', 'attr' => array('class' => "ckeditor",'style' => 'width:100%')))
            ->add('publishDate','date',array('label'  => 'تاریخ انتشار', 'attr' => array('style' => 'height:25px;margin-bottom:10px;')))
            ->add('submit', 'submit', array('label'  => 'ویرایش مطلب', 'attr' => array("class" => "btn")))
            ->getForm();
        $article_form->handleRequest($request);
        if ($article_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->get('session')->getFlashBag()->add('adminSuccess', 'مطلب '.$article->getTitle().' با موفقیت ویرایش شد.');
        }
        return $this->render('bloggerblogBundle:AdminBlog:editArticle.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "article_form" => $article_form->createView()));
    }
    public function showRecentCommentsAction($articleId){
        $user = $this->getUser();
        if($articleId == "all")
            $commentsRes = $this->getDoctrine() ->getRepository('bloggerblogBundle:Comment') ->findBy(array("user" => $user->getId()),array("date" => 'DESC',"id" => 'DESC'));
        elseif($articleId == "notApprove")
            $commentsRes = $this->getDoctrine() ->getRepository('bloggerblogBundle:Comment') ->findBy(array("user" => $user->getId(),"confirmed" => false),array("date" => 'DESC',"id" => 'DESC'));
        else
            $commentsRes = $this->getDoctrine() ->getRepository('bloggerblogBundle:Comment') ->findBy(array("user" => $user->getId(),"article" => $articleId),array("date" => 'DESC',"id" => 'DESC'));
        $comments = array();
        foreach($commentsRes as $singleComment){
            $article_comment = $singleComment->getArticle();
//            $singleComment = new Comment();
            $comments[] = array("id" =>$singleComment->getId(),
                "name" => $singleComment->getName(),
                "comment" => $singleComment->getComment(),
                "date" => date_format($singleComment->getDate(),"Y-m-d"),
                "confirmed" => $singleComment->getConfirmed(),
                "articleTitle" => $article_comment->getTitle(),
                "articleAddress" => $article_comment->getAddress());
        }
        return $this->render('bloggerblogBundle:AdminBlog:showRecentComments.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                'comments' => $comments,'showType' => $articleId));
    }
    public function approveCommentAction($id,$articleId){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('bloggerblogBundle:Comment')->findOneBy(array("user" => $user, "id" => $id));
        if (!$comment) {
            throw $this->createNotFoundException(
                "خطا: این نظر پیدا نشد.<br />"
            );
        }
        $comment->setConfirmed(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
        $this->get('session')->getFlashBag()->add('adminSuccess', 'نظر '.$comment->getName().' با موفقیت تائید شد.');
        return $this->redirect($this->generateUrl('bloggerblog_blogAdminShowRecentComments',array('articleId' => $articleId)));
    }
    public function editProfileAction(Request $request){
        $user = $this->getUser();
        $user_form = $this->createFormBuilder($user)
            ->add('blogName','text',array('label'  => 'عنوان وبلاگ', 'attr' => array('style' => 'height:25px')))
            ->add('blogDescription','textarea',array('label'  => 'درباره وبلاگ','required' => false, 'attr' => array('style' => 'max-width: 100%;min-width: 100%;min-height: 100px;')))
            ->add('email','email',array('label'  => 'ایمیل', 'attr' => array('style' => 'height:25px; direction: ltr;text-align:left;height:30px')))
            ->add('name','text',array('label'  => 'نام','required' => false, 'attr' => array('style' => 'height:25px')))
            ->add('family','text',array('label'  => 'نام خانوادگی','required' => false, 'attr' => array('style' => 'height:25px')))
            ->add('submit', 'submit', array('label'  => 'ویرایش', 'attr' => array("class" => "btn")))
            ->getForm();
        $user_form->handleRequest($request);
        if ($user_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('adminSuccess', 'پروفایل با موفقیت ویرایش شد.');
        }
        return $this->render('bloggerblogBundle:AdminBlog:editProfile.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "user_form" => $user_form->createView()));
    }

    public function editCommentAction($id,$articleId,Request $request){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('bloggerblogBundle:Comment')->findOneBy(array("id" => $id,"user" => $user));
        if (!$comment) {
            throw $this->createNotFoundException(
                "خطا: این نظر پیدا نشد<br />"
            );
        }

        $comment_form = $this->createFormBuilder($comment)
            ->add('comment','textarea',array('label'  => 'نظر', 'attr' => array('class' => "ckeditor")))
            ->add('submit', 'submit', array('label'  => 'ویرایش نظر', 'attr' => array("class" => "btn")))
            ->getForm();
        $comment_form->handleRequest($request);
        if ($comment_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $this->get('session')->getFlashBag()->add('adminSuccess', 'نظر '.$comment->getName().' با موفقیت ویرایش شد.');
            return $this->redirect($this->generateUrl('bloggerblog_blogAdminShowRecentComments',array('articleId' => 'all')));
        }
        return $this->render('bloggerblogBundle:AdminBlog:editComment.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "comment_form" => $comment_form->createView(), "comment_name" => $comment->getName(),
                "articleId" => $articleId));
    }
}
