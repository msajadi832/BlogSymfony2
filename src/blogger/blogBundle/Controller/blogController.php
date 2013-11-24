<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\Article;
use blogger\blogBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class blogController extends Controller
{
    public function blogAction($blog_name)
    {
        var_dump($blog_name);
        $user = $this->getDoctrine() ->getRepository('bloggerblogBundle:User') ->find(1);

        $articlesa = $user->getArticles();
        $articles = array();
        foreach($articlesa as $singleArticle){
            $articles[] = array("article_title" => $singleArticle->getTitle(), "article_date" => date_format($singleArticle->getPublishDate(),"Y-m-d"),
                "article_body" => $singleArticle->getBody(),"article_comment_count" => $singleArticle->getComments()->count());
        }


        $comments_doc = $this->getDoctrine()  ->getRepository('bloggerblogBundle:Comment')->findAll();
        $recent_comment = array();
        foreach($comments_doc as $singleComment){
            $recent_comment[] = array("name" => $singleComment->getName(), "comment" => $singleComment->getComment());
        }

        return $this->render('bloggerblogBundle:Blog/homepage:homepage.html.twig',
            array('blog_name' => $user->getBlogName(), "sidebar_name" => "نظرات اخیر","recent_comments" => $recent_comment,"articles" => $articles));
    }
    public function articleAction($blog_name,$article_name)
    {
        var_dump($blog_name);
        $recent_comment = array(array("name" => "علی", "comment" => "سلام"),array("name" => "حسین", "comment" => "بیا"),
            array("name" => "رضا", "comment" => "بای"));

        $comment_article = array(array("name" => "علی", "date" => "12345", "comment" => "سلام"),array("name" => "حسین", "date" => "54321", "comment" => "بیا"),
            array("name" => "رضا", "date" => "777777", "comment" => "بای"));

        return $this->render('bloggerblogBundle:Blog/Article:Article.html.twig',
            array('blog_name' => $blog_name, "article_title" => $article_name,
                "sidebar_name" => "نظرات اخیر","recent_comments" => $recent_comment,
            "article_date" => "1392/2/17", "article_body" => "سلام این اولین مقاله من است.",
            "article_comments" => $comment_article));
    }
}
