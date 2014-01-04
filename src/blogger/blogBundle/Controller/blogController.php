<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\Article;
use blogger\blogBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class blogController extends Controller
{
    public function blogAction($blog_name)
    {
        $user = $this->getDoctrine()->getRepository('bloggerblogBundle:User') ->findOneBy(array('blogAddress' => $blog_name));

        $articlesa = $user->getArticles();
        $articles = array();
        foreach($articlesa as $singleArticle){
            $articles[] = array("article_title" => $singleArticle->getTitle(),"article_address" => $singleArticle->getAddress(), "article_date" => $singleArticle->getPublishDate(),
                "article_body" => (strlen($singleArticle->getBody())> 500)?mb_substr($singleArticle->getBody(),0,500, 'UTF-8')." ...":$singleArticle->getBody(),
                "article_comment_count" => $singleArticle->getComments()->count());
        }

        $comments_doc = $this->getDoctrine()->getRepository('bloggerblogBundle:Comment')->findBy(array("user" => $user->getId()),array("date" => 'DESC'),10);
        $recent_comments = array();
        foreach($comments_doc as $singleComment){
            $recent_comments[] = array("article_address" => $singleComment->getArticle()->getAddress(),"id" =>$singleComment->getId(),
                "name" => (strlen($singleComment->getName())> 50)?mb_substr($singleComment->getName(),0,50, 'UTF-8')." ...":$singleComment->getName(),
                "date" => $singleComment->getDate(),
                "comment" => (strlen($singleComment->getComment())> 150)?mb_substr($singleComment->getComment(),0,150, 'UTF-8')." ...":$singleComment->getComment());
        }

        return $this->render('bloggerblogBundle:Blog/homepage:homepage.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "sidebar_name" => "نظرات اخیر", "recent_comments" => $recent_comments,"articles" => $articles));
    }
    public function articleAction($blog_name,$article_name,Request $request)
    {
        $user = $this->getDoctrine()->getRepository('bloggerblogBundle:User') ->findOneBy(array('blogAddress' => $blog_name));
        $article = $this->getDoctrine() ->getRepository('bloggerblogBundle:Article') ->findOneBy(array("user" => $user->getId(),"address" => $article_name));
        $comments = $this->getDoctrine() ->getRepository('bloggerblogBundle:Comment') ->findBy(array("user" => $user->getId(),"article" => $article->getId()),array("date" => 'DESC',"id" => 'DESC'));
        $comment_article = array();
        foreach($comments as $singleComment){
            $comment_article[] = array("id" =>$singleComment->getId(),
                "name" => $singleComment->getName(),
            "date" => date_format($singleComment->getDate(),"Y-m-d"),
            "comment" => $singleComment->getComment());
        }
        $article_doc = $this->getDoctrine()->getRepository('bloggerblogBundle:Article')->findBy(array("user" => $user->getId()),array("publishDate" => 'DESC'),10);
        $recent_articles = array();
        foreach($article_doc as $singleArticle){
            $recent_articles[] = array("title" => $singleArticle->getTitle(), "address" => $singleArticle->getAddress());
        }

        $comment = new Comment();
        $comment->setArticle($article);
        $comment->setUser($user);
        $comment->setDate(new \DateTime());

        $comment_form = $this->createFormBuilder($comment)
            ->add('name','text',array('label'  => 'نام', 'attr' => array('style' => 'height:25px')))
            ->add('comment','textarea',array('label'  => 'نظر', 'attr' => array('style' => 'width:100%;max-width:100%;min-width:100%;')))
            ->add('submit', 'submit', array('label'  => 'ثبت نظر'))
            ->getForm();

        $comment_form->handleRequest($request);

        if ($comment_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('bloggerblog_blogArticle',array('article_name' => $article_name,'blog_name' => $blog_name)));
        }

        return $this->render('bloggerblogBundle:Blog/Article:Article.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "article_info" => array("title" => $article->getTitle(), "date" => $article->getPublishDate(),"body" => $article->getBody(),"comments" => $comment_article),
                "sidebar_name" => "مقالات اخیر","recent_articles" => $recent_articles,
            "comment_form" => $comment_form->createView()));
    }
}
