<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\Article;
use blogger\blogBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class blogController extends Controller
{
    public function blogAction($blog_name)
    {
        $user = $this->getDoctrine()->getRepository('bloggerblogBundle:User') ->findOneBy(array('blogAddress' => $blog_name));

        $articlesa = $user->getArticles();
        $articles = array();
        foreach($articlesa as $singleArticle){
            $articles[] = array("article_title" => $singleArticle->getTitle(),"article_address" => $singleArticle->getAddress(), "article_date" => date_format($singleArticle->getPublishDate(),"Y-m-d"),
                "article_body" => $singleArticle->getBody(),"article_comment_count" => $singleArticle->getComments()->count());
        }

        $comments_doc = $this->getDoctrine()->getRepository('bloggerblogBundle:Comment')->findBy(array("user" => $user->getId()),array("date" => 'DESC'),10);
        $recent_comment = array();
        foreach($comments_doc as $singleComment){
            $recent_comment[] = array("article_id" => $singleComment->getArticle()->getAddress(),"id" =>$singleComment->getId(),"name" => $singleComment->getName(), "comment" => $singleComment->getComment());
        }

        return $this->render('bloggerblogBundle:Blog/homepage:homepage.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()), "sidebar_name" => "نظرات اخیر",
                "recent_comments" => $recent_comment,"articles" => $articles));
    }
    public function articleAction($blog_name,$article_name)
    {
        $user = $this->getDoctrine()->getRepository('bloggerblogBundle:User') ->findOneBy(array('blogAddress' => $blog_name));
        $article = $this->getDoctrine() ->getRepository('bloggerblogBundle:Article') ->findOneBy(array("user" => $user->getId(),"address" => $article_name));
        $comment_article = array();
        foreach($article->getComments() as $singleComment){
            $comment_article[] = array("id" =>$singleComment->getId(),"name" => $singleComment->getName(), "date" => date_format($singleComment->getDate(),"Y-m-d"), "comment" => $singleComment->getComment());
        }
        $comments_doc = $this->getDoctrine()->getRepository('bloggerblogBundle:Comment')->findBy(array("user" => $user->getId()),array("date" => 'DESC'),10);
        $recent_comment = array();
        foreach($comments_doc as $singleComment){
            $recent_comment[] = array("article_id" => $singleComment->getArticle()->getAddress(),"id" =>$singleComment->getId(),"name" => $singleComment->getName(), "comment" => $singleComment->getComment());
        }


        return $this->render('bloggerblogBundle:Blog/Article:Article.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "article_title" => $article->getTitle(),
                "sidebar_name" => "نظرات اخیر","recent_comments" => $recent_comment,
            "article_date" => date_format($article->getPublishDate(),"Y-m-d"), "article_body" => $article->getBody(),
            "article_comments" => $comment_article));
    }
}
