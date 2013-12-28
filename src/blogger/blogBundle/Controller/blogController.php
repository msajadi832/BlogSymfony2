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
            $articles[] = array("article_title" => $singleArticle->getTitle(),"article_address" => $singleArticle->getAddress(), "article_date" => $singleArticle->getPublishDate(),
                "article_body" => $singleArticle->getBody(),"article_comment_count" => $singleArticle->getComments()->count());
        }

        $comments_doc = $this->getDoctrine()->getRepository('bloggerblogBundle:Comment')->findBy(array("user" => $user->getId()),array("date" => 'DESC'),10);
        $recent_comments = array();
        foreach($comments_doc as $singleComment){
            $recent_comments[] = array("article_address" => $singleComment->getArticle()->getAddress(),"id" =>$singleComment->getId(),
                "name" => $singleComment->getName(), "date" => $singleComment->getDate(), "comment" => $singleComment->getComment());
        }

        return $this->render('bloggerblogBundle:Blog/homepage:homepage.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "sidebar_name" => "نظرات اخیر", "recent_comments" => $recent_comments,"articles" => $articles));
    }
    public function articleAction($blog_name,$article_name)
    {
        $user = $this->getDoctrine()->getRepository('bloggerblogBundle:User') ->findOneBy(array('blogAddress' => $blog_name));
        $article = $this->getDoctrine() ->getRepository('bloggerblogBundle:Article') ->findOneBy(array("user" => $user->getId(),"address" => $article_name));
        $comment_article = array();
        foreach($article->getComments() as $singleComment){
            $comment_article[] = array("id" =>$singleComment->getId(),"name" => $singleComment->getName(), "date" => date_format($singleComment->getDate(),"Y-m-d"), "comment" => $singleComment->getComment());
        }
        $article_doc = $this->getDoctrine()->getRepository('bloggerblogBundle:Article')->findBy(array("user" => $user->getId()),array("publishDate" => 'DESC'),10);
        $recent_articles = array();
        foreach($article_doc as $singleArticle){
            $recent_articles[] = array("title" => $singleArticle->getTitle(), "address" => $singleArticle->getAddress());
        }


        return $this->render('bloggerblogBundle:Blog/Article:Article.html.twig',
            array('blog_info' => array('name' => $user->getBlogName(), 'address'=> $user->getBlogAddress()),
                "article_info" => array("title" => $article->getTitle(), "date" => $article->getPublishDate(),"body" => $article->getBody(),"comments" => $comment_article),
                "sidebar_name" => "مقالات اخیر","recent_articles" => $recent_articles));
    }
}
