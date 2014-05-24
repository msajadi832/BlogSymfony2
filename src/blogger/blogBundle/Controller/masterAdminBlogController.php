<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\Article;
use blogger\blogBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class masterAdminBlogController extends Controller
{

    public function dashboardAction()
    {
        return $this->render('bloggerblogBundle:MasterAdminBlog:dashboard.html.twig');
    }
}
