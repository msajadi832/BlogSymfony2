<?php

namespace blogger\blogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class blogController extends Controller
{
    public function blogAction($blog_name)
    {
        return $this->render('bloggerblogBundle:Blog/Article:Article.html.twig',
            array('blog_name' => $blog_name, "content_name" => "نظرات اخیر", "sidebar_name" => "نظرات اخیر"));
    }
    public function articleAction($blog_name,$article_name)
    {
        $recent_comment = array(array("name" => "علی", "comment" => "سلام"),array("name" => "حسین", "comment" => "بیا"),
            array("name" => "رضا", "comment" => "بای"));

        return $this->render('bloggerblogBundle:Blog/Article:Article.html.twig',
            array('blog_name' => $blog_name, "content_name" => $article_name,
                "sidebar_name" => "نظرات اخیر","recent_comments" => $recent_comment,
            "article_date" => "1392/2/17", "article_body" => "سلام این اولین مقاله من است."));
    }
}
