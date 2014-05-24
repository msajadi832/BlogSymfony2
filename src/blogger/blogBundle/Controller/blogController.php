<?php

namespace blogger\blogBundle\Controller;

use blogger\blogBundle\Entity\Article;
use blogger\blogBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class blogController extends Controller
{
    private $num_list_blog = 2;


    private function hideBlock($retTemplate, $name){
        $begin = '<'.$name.'>';
        $end = '</'.$name.'>';
        $beginPlace = strpos($retTemplate,$begin);
        $endPlace = strpos($retTemplate,$end);

        if( $beginPlace && $endPlace){
            $lenContinue = strlen($end) + ($endPlace - $beginPlace);
            $retTemplate = substr_replace($retTemplate,'',$beginPlace,$lenContinue);
        }

        return $retTemplate;
    }

    private function replaceVariable($retTemplate, $search, $replace, $father = ""){
        $retTemplate = str_replace('<-'.$search.'->','{{ '.$father.$replace.' }}',$retTemplate);
        return $retTemplate;
    }

    private function replaceText($retTemplate, $search, $replace){
        $retTemplate = str_replace('<-'.$search.'->',$replace,$retTemplate);
        return $retTemplate;
    }

    private function replaceStartEndBlock($retTemplate, $search, $replaceStart, $replaceEnd){
        $retTemplate = str_replace('<'.$search.'>',$replaceStart,$retTemplate);
        $retTemplate = str_replace('</'.$search.'>',$replaceEnd,$retTemplate);
        return $retTemplate;
    }

    private function hideBlockName($retTemplate, $name){
        $retTemplate = str_replace('<'.$name.'>','',$retTemplate);
        $retTemplate = str_replace('</'.$name.'>','',$retTemplate);
        return $retTemplate;
    }

    private function createMainDetails($retTemplate){
        $retTemplate = $this->replaceVariable($retTemplate, 'BlogName', 'name', 'Blog.');
        $retTemplate = $this->replaceVariable($retTemplate, 'BlogAddress', 'address', 'Blog.');
        $retTemplate = $this->replaceVariable($retTemplate, 'BlogEmail', 'email', 'Blog.');
        $retTemplate = $this->replaceVariable($retTemplate, 'BlogAbout', 'about', 'Blog.');
        return $retTemplate;
    }

    private function createPostList($retTemplate, $start, $count){
        $retTemplate = $this->createMainDetails($retTemplate);
        $retTemplate = $this->recentComments($retTemplate);
        $retTemplate = $this->recentPosts($retTemplate);

        if( (strpos($retTemplate,'<PostList>')) ){
            if((strpos($retTemplate,'</PostList>'))){
                $retTemplate = $this->hideBlockName($retTemplate, 'PostList');
                $retTemplate = $this->hideBlock($retTemplate, 'PostDetail');
                if(strpos($retTemplate,'<SinglePost>')){
                    if(strpos($retTemplate,'</SinglePost>')){
                        $retTemplate = $this->replaceStartEndBlock($retTemplate, 'SinglePost', '{% for post in posts %}', '{% endfor %}');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostTitle', 'article_title', 'post.');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostContent', 'article_body | raw', 'post.');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostLastEdit', 'article_date', 'post.');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostCommentCount', 'article_comment_count', 'post.');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostAddress', 'article_address', 'post.');

                    }else{
                        $retTemplate = str_replace('<SinglePost>','تگ بسته &lt;SinglePost/&gt; موجود نیست یا اشتباه است',$retTemplate);
                    }
                }
                $nextPage = $prevPage = false;
                if($start < $count){
                    $nextPage = true;
                }
                if($start > 1){
                    $prevPage = true;
                }
                $retTemplate = $this->pagination($retTemplate, 'PostList', $nextPage, $prevPage);
            }else{
                $retTemplate = str_replace('<PostList>','تگ بسته &lt;PostList/&gt; موجود نیست یا اشتباه است',$retTemplate);
            }
        }


        return $retTemplate;
    }

    private function createDetailPost($retTemplate){
        $retTemplate = $this->createMainDetails($retTemplate);
        $retTemplate = $this->recentComments($retTemplate);
        $retTemplate = $this->recentPosts($retTemplate);

        if( (strpos($retTemplate,'<PostDetail>')) ){
            if((strpos($retTemplate,'</PostDetail>'))){
                $retTemplate = $this->hideBlockName($retTemplate, 'PostDetail');
                $retTemplate = $this->hideBlock($retTemplate, 'PostList');

                #main Detail Article
                $retTemplate = $this->replaceVariable($retTemplate, 'PostDetailAddress','address','PostDetail.');
                $retTemplate = $this->replaceVariable($retTemplate, 'PostDetailTitle','title','PostDetail.');
                $retTemplate = $this->replaceVariable($retTemplate, 'PostDetailContent','body | raw','PostDetail.');
                $retTemplate = $this->replaceVariable($retTemplate, 'PostDetailLastEdit','date','PostDetail.');

                #Comments Detail for Article
                if(strpos($retTemplate,'<PostDetailComments>')){
                    if(strpos($retTemplate,'</PostDetailComments>')){
                        if(strpos($retTemplate,'<SinglePostComment>')){
                            if(strpos($retTemplate,'</SinglePostComment>')){
                                $retTemplate = $this->replaceStartEndBlock($retTemplate, 'SinglePostComment', '{% for comment in comments %}', '{% endfor %}');
                                $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostCommentId', 'id', 'comment.');
                                $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostCommentLink', 'address', 'comment.');
                                $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostCommentName', 'name', 'comment.');
                                $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostCommentContent', 'content | raw', 'comment.');
                                $retTemplate = $this->replaceVariable($retTemplate, 'SinglePostCommentDate', 'date', 'comment.');
                            }else{
                                $retTemplate = str_replace('<SinglePostComment>','تگ بسته &lt;SinglePostComment/&gt; موجود نیست یا اشتباه است',$retTemplate);
                            }
                        }
                    }else{
                        $retTemplate = str_replace('<PostDetailComments>','تگ بسته &lt;PostDetailComments/&gt; موجود نیست یا اشتباه است',$retTemplate);
                    }
                }

                #New Comment For Article Flashbag
                if(strpos($retTemplate,'<PostDetailNewCommentAlert>')){
                    if(strpos($retTemplate,'</PostDetailNewCommentAlert>')){
                        $retTemplate = $this->replaceStartEndBlock($retTemplate, 'PostDetailNewCommentAlert', '{% for flashMessage in app.session.flashbag.get("commentAddSuccess") %}', '{% endfor %}');
                        $retTemplate = $this->replaceVariable($retTemplate, 'PostDetailNewCommentAlertContent','flashMessage|raw');
                    }
                }else{
                    $retTemplate = str_replace('<PostDetailNewCommentAlert>','تگ بسته &lt;PostDetailNewComment/&gt; موجود نیست یا اشتباه است',$retTemplate);
                }

                #New Comment For Article
                if(strpos($retTemplate,'<PostDetailNewComment>')){
                    if(strpos($retTemplate,'</PostDetailNewComment>')){
                        $retTemplate = $this->replaceText($retTemplate, 'PostDetailNewCommentName','name="NewCommentName" required="required"');
                        $retTemplate = $this->replaceText($retTemplate, 'PostDetailNewCommentContent','name="NewCommentContent" required="required"');
                    }
                }else{
                    $retTemplate = str_replace('<PostDetailNewComment>','تگ بسته &lt;PostDetailNewComment/&gt; موجود نیست یا اشتباه است',$retTemplate);
                }



                return $retTemplate;

            }else{
                $retTemplate = str_replace('<PostDetail>','تگ بسته &lt;PostDetail/&gt; موجود نیست یا اشتباه است',$retTemplate);
            }
        }
        return $retTemplate;
    }

    private function recentComments($retTemplate){
        if( (strpos($retTemplate,'<RecentComments>')) ){
            if((strpos($retTemplate,'</RecentComments>'))){
                $retTemplate = $this->hideBlockName($retTemplate, 'RecentComments');
                if(strpos($retTemplate,'<SingleRecentComment>')){
                    if(strpos($retTemplate,'</SingleRecentComment>')){
                        $retTemplate = $this->replaceStartEndBlock($retTemplate, 'SingleRecentComment', '{% for comment in recentComments %}', '{% endfor %}');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SingleRecentCommentLink', 'address', 'comment.');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SingleRecentCommentName', 'name', 'comment.');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SingleRecentCommentDate', 'date', 'comment.');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SingleRecentCommentContent', 'content | raw', 'comment.');
                    }else{
                        $retTemplate = str_replace('<SingleRecentComment>','تگ بسته &lt;SingleRecentComment/&gt; موجود نیست یا اشتباه است',$retTemplate);
                    }
                }
            }else{
                $retTemplate = str_replace('<RecentComments>','تگ بسته &lt;RecentComments/&gt; موجود نیست یا اشتباه است',$retTemplate);
            }
        }
        return $retTemplate;
    }

    private function recentPosts($retTemplate){
        if( (strpos($retTemplate,'<RecentPosts>')) ){
            if((strpos($retTemplate,'</RecentPosts>'))){
                $retTemplate = $this->hideBlockName($retTemplate, 'RecentPosts');
                if(strpos($retTemplate,'<SingleRecentPost>')){
                    if(strpos($retTemplate,'</SingleRecentPost>')){
                        $retTemplate = $this->replaceStartEndBlock($retTemplate, 'SingleRecentPost', '{% for post in recentPosts %}', '{% endfor %}');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SingleRecentPostAddress', 'address', 'post.');
                        $retTemplate = $this->replaceVariable($retTemplate, 'SingleRecentPostTitle', 'title', 'post.');
                    }else{
                        $retTemplate = str_replace('<SingleRecentPost>','تگ بسته &lt;SingleRecentPost/&gt; موجود نیست یا اشتباه است',$retTemplate);
                    }
                }
            }else{
                $retTemplate = str_replace('<RecentPosts>','تگ بسته &lt;RecentPosts/&gt; موجود نیست یا اشتباه است',$retTemplate);
            }
        }
        return $retTemplate;
    }

    private function pagination($retTemplate,$part,$show_next,$show_prev){
        if($show_next){
            if( (strpos($retTemplate,'<'.$part.'NextPage>')) ){
                if((strpos($retTemplate,'</'.$part.'NextPage>'))){
                    $retTemplate = $this->hideBlockName($retTemplate, $part.'NextPage');
                    $retTemplate = $this->replaceVariable($retTemplate, $part.'NextPageAddress', 'next', $part.'Pagination.');
                }else{
                    $retTemplate = str_replace('<'.$part.'NextPage>','تگ بسته &lt;'.$part.'NextPage/&gt; موجود نیست یا اشتباه است',$retTemplate);
                }
            }
        }else{
            $retTemplate = $this->hideBlock($retTemplate, $part.'NextPage');
        }
        if($show_prev){
            if( (strpos($retTemplate,'<'.$part.'PreviousPage>')) ){
                if((strpos($retTemplate,'</'.$part.'PreviousPage>'))){
                    $retTemplate = $this->hideBlockName($retTemplate, $part.'PreviousPage');
                    $retTemplate = $this->replaceVariable($retTemplate, $part.'PreviousPageAddress', 'prev', $part.'Pagination.');
                }else{
                    $retTemplate = str_replace('<'.$part.'PreviousPage>','تگ بسته &lt;'.$part.'PreviousPage/&gt; موجود نیست یا اشتباه است',$retTemplate);
                }
            }
        }else{
            $retTemplate = $this->hideBlock($retTemplate, $part.'PreviousPage');
        }
        return $retTemplate;
    }

//    private function CreateTemplate($retTemplate, $isHomePage = False){
//        $retTemplate = $this->createMainDetails($retTemplate);
////        $retTemplate = ($isHomePage)?$this->createPostList($retTemplate):$this->createDetailPost($retTemplate);
//
//        $retTemplate = $this->recentComments($retTemplate);
//        $retTemplate = $this->recentPosts($retTemplate);
//
//        return $retTemplate;
//    }

    private function renderTwig($retTemplate, array $context = array()){
        $twig = new \Twig_Environment(new \Twig_Loader_String());
        foreach( $this->get('twig')->getExtensions() as $ext ) {
            $twig->addExtension( $ext );
        }
        return new Response($twig->render($retTemplate, $context));
    }

    private function blogData($user){
        return array("address"=>$this->generateUrl('bloggerblog_blogHomepage',array('blog_name'=>$user->getUsername())), "name"=>$user->getBlogName(), "email"=>str_replace("@"," (at) ", $user->getEmail()),
            "about"=>$user->getBlogDescription());
    }

    private function recentCommentsData($user){
        $comments_doc = $this->getDoctrine()->getRepository('bloggerblogBundle:Comment')->findBy(array("user" => $user->getId(),"confirmed" => true),array("date" => 'DESC'),10);
        $recent_comments = array();
        foreach($comments_doc as $singleComment){
            $recent_comments[] = array("name" => (strlen($singleComment->getName())> 50)?mb_substr($singleComment->getName(),0,50, 'UTF-8')." ...":$singleComment->getName(),
                "address"=>$this->generateUrl('bloggerblog_blogArticle', array('blog_name'=>$user->getUsername(),'article_name' => $singleComment->getArticle()->getAddress())).'#comment'.$singleComment->getId(),
                "date" => $this->get('my_date_convert')->MiladiToShamsi($singleComment->getDate()),
                "content" => strip_tags((strlen($singleComment->getComment())> 150)?mb_substr($singleComment->getComment(),0,150, 'UTF-8')." ...":$singleComment->getComment()));
        }
        return $recent_comments;
    }

    private function recentPostsData($user){
        $articleRepo = $this->getDoctrine() ->getRepository('bloggerblogBundle:Article');
        $qb = $articleRepo->createQueryBuilder('a')
            ->where("a.user = :user")
            ->setParameter("user",$user->getId())
            ->andWhere('a.publishDate < :now')
            ->setParameter("now",new \DateTime())
            ->orderBy('a.publishDate', 'DESC')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery();
        $articlesa =$qb->getResult();
        $recent_articles = array();
        foreach($articlesa as $singleArticle){
            $recent_articles[] = array("title" => $singleArticle->getTitle(),
                "address" => $this->generateUrl('bloggerblog_blogArticle', array('blog_name'=>$user->getUsername(),'article_name' => $singleArticle->getAddress())));
        }
        return $recent_articles;
    }

    public function blogAction($blog_name,$start)
    {
        $user = $this->getDoctrine()->getRepository('bloggerblogBundle:User') ->findOneBy(array('username' => $blog_name));

        $article = $this->getDoctrine()->getRepository('bloggerblogBundle:Article');
        $qb = $article->createQueryBuilder('a')
            ->where("a.user = :user")
            ->setParameter("user",$user->getId())
            ->andWhere('a.publishDate < :now')
            ->setParameter("now",new \DateTime())
            ->orderBy('a.id', 'DESC');

        $count = ceil(count($qb->getQuery()->getResult())/$this->num_list_blog);

        $articlesa =$qb->setFirstResult(($start-1)*$this->num_list_blog)
            ->setMaxResults($this->num_list_blog )->getQuery()->getResult();
        $articles = array();
        foreach($articlesa as $singleArticle){
            $comment_article_count = count($this->getDoctrine() ->getRepository('bloggerblogBundle:Comment') ->findBy(array("user" => $user->getId(),"article" => $singleArticle->getId(),"confirmed" => true)));
            $articles[] = array("article_title" => $singleArticle->getTitle(),
                "article_address" => $this->generateUrl('bloggerblog_blogArticle', array('blog_name'=>$user->getUsername(),'article_name' => $singleArticle->getAddress())),
                "article_date" => $this->get('my_date_convert')->MiladiToShamsi($singleArticle->getPublishDate()),
                "article_body" => (strlen($singleArticle->getBody())> 500)?mb_substr($singleArticle->getBody(),0,500, 'UTF-8')." ...":$singleArticle->getBody(),
                "article_comment_count" => $comment_article_count);
        }



        $pagination = array("next"=>$this->generateUrl('bloggerblog_blogHomepage',array('blog_name'=> $user->getUsername(),'start'=> $start+1)),
                            "prev"=>$this->generateUrl('bloggerblog_blogHomepage',array('blog_name'=> $user->getUsername(),'start'=> $start-1)));


        $theme = $user->getBlogTemplate();
        $retTemplate = $this->createPostList($theme, $start, $count);

        return $this->renderTwig($retTemplate,array('Blog' => $this->blogData($user), 'posts' => $articles, 'PostListPagination'=>$pagination,
            'recentComments'=>$this->recentCommentsData($user), 'recentPosts'=>$this->recentPostsData($user)));
    }

    public function articleAction($blog_name,$article_name, Request $request)
    {
        $params = $request->request->all();
        $user = $this->getDoctrine()->getRepository('bloggerblogBundle:User') ->findOneBy(array('username' => $blog_name));
        $articleRepo = $this->getDoctrine() ->getRepository('bloggerblogBundle:Article');
        $article = $articleRepo->findOneBy(array("user" => $user->getId(),"address" => $article_name));

        if($params != []){
            $comment = new Comment();
            $comment->setArticle($article);
            $comment->setUser($user);
            $comment->setDate(new \DateTime());
            $comment->setConfirmed(false);
            $comment->setName($params['NewCommentName']);
            $comment->setComment($params['NewCommentContent']);
//            return new Response("data is: NewCommentName=" .$params['NewCommentName']. "  NewCommentContent=".$params['NewCommentContent']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $this->get('session')->getFlashBag()->add('commentAddSuccess', '<h4>نظر شما با موفقیت ثبت شد! </h4>نظر شما بعد از تایید نمایش داده خواهد شد.');
        }

        $comments = $this->getDoctrine() ->getRepository('bloggerblogBundle:Comment') ->findBy(array("user" => $user->getId(),"article" => $article->getId(),"confirmed" => true),array("date" => 'DESC',"id" => 'DESC'));
        $comment_article = array();
        foreach($comments as $singleComment){
            $comment_article[] = array(
                "id" =>'comment'.$singleComment->getId(),
                "address" =>$this->generateUrl('bloggerblog_blogArticle', array('blog_name'=>$user->getUsername(),'article_name' => $singleComment->getArticle()->getAddress())).'#comment'.$singleComment->getId(),
                "name" => $singleComment->getName(),
                "date" => $this->get('my_date_convert')->MiladiToShamsi($singleComment->getDate()),
                "content" => $singleComment->getComment());
        }


        $PostDetail = array("address"=> $article->getAddress(), "title"=>$article->getTitle(), "body"=>$article->getBody(),"date"=>$this->get('my_date_convert')->MiladiToShamsi($article->getPublishDate()));

        $theme = $user->getBlogTemplate();
        $retTemplate = $this->createDetailPost($theme);

        return $this->renderTwig($retTemplate,array('Blog' => $this->blogData($user), 'PostDetail' => $PostDetail, 'comments'=>$comment_article,
            'recentComments'=>$this->recentCommentsData($user), 'recentPosts'=>$this->recentPostsData($user)));

    }


}
