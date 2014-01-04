<?php

namespace blogger\blogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('bloggerblogBundle:Default:index.html.twig');
    }

    public function blogAdminAction()
    {
        return $this->render('bloggerblogBundle:AdminBlog/addBlog:addBlog.html.twig');
    }
}
