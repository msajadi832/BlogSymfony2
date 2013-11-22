<?php

namespace blogger\blogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class blogController extends Controller
{
    public function blogAction($blog_name)
    {
        return $this->render('bloggerblogBundle:Blog/Article:Article.html.twig', array('blog_name' => $blog_name, "sidebar_name" => "نظرات اخیر"));
    }
}
