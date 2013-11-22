<?php

namespace blogger\blogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class blogController extends Controller
{
    public function blogAction($blog_name)
    {
        $recent_comment = array(array("name" => "علی", "comment" => "سلام"),array("name" => "حسین", "comment" => "بیا"),
            array("name" => "رضا", "comment" => "بای"));

        $articles = array(array("article_title" => "علی", "article_date" => "12345", "article_body" => "سلام", "article_comment_count" => 5),
            array("article_title" => "کتاب", "article_date" => "234234234", "article_body" => "مسنبدخهب هخبنمتثص بخهتس یبمنت خ", "article_comment_count" => 10),
            array("article_title" => "یه مقاله خوب", "article_date" => "56452", "article_body" => "حخصم نصتبح خ  سیتبح هب نتسیبحثصخه ", "article_comment_count" => 50));

        return $this->render('bloggerblogBundle:Blog/homepage:homepage.html.twig',
            array('blog_name' => $blog_name, "sidebar_name" => "نظرات اخیر","recent_comments" => $recent_comment,"articles" => $articles));
    }
    public function articleAction($blog_name,$article_name)
    {
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
