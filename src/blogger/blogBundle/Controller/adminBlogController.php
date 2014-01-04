<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\Article;
use blogger\blogBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class adminBlogController extends Controller
{
    public function showAllAction()
    {
        $user = $this->getUser();

        $articlesa = $user->getArticles();
        $articles = array();
        foreach($articlesa as $singleArticle){
            $articles[] = array("article_title" => $singleArticle->getTitle(),"article_address" => $singleArticle->getAddress(), "article_date" => $singleArticle->getPublishDate(),
                "article_body" => (strlen($singleArticle->getBody())> 500)?mb_substr($singleArticle->getBody(),0,500, 'UTF-8')." ...":$singleArticle->getBody(),
                "article_comment_count" => $singleArticle->getComments()->count());
        }

//        $comments_doc = $this->getDoctrine()->getRepository('bloggerblogBundle:Comment')->findBy(array("user" => $user->getId()),array("date" => 'DESC'),10);
//        $recent_comments = array();
//        foreach($comments_doc as $singleComment){
//            $recent_comments[] = array("article_address" => $singleComment->getArticle()->getAddress(),"id" =>$singleComment->getId(),
//                "name" => (strlen($singleComment->getName())> 50)?mb_substr($singleComment->getName(),0,50, 'UTF-8')." ...":$singleComment->getName(),
//                "date" => $singleComment->getDate(),
//                "comment" => (strlen($singleComment->getComment())> 150)?mb_substr($singleComment->getComment(),0,150, 'UTF-8')." ...":$singleComment->getComment());
//        }

        return $this->render('bloggerblogBundle:AdminBlog:showAll.html.twig',
            array("articles" => $articles));
    }
}
