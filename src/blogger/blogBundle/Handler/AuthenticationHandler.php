<?php
/**
 * Created by PhpStorm.
 * User: omidnematollahi
 * Date: 12/16/13
 * Time: 1:17 AM
 */

namespace blogger\blogBundle\Handler;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface
{

    protected $router;
    protected $security;

    public function __construct(Router $router, SecurityContext $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return RedirectResponse never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_ADMIN'))
            return new RedirectResponse($this->router->generate('bloggerblog_blogSuperAdmin'));

//        if ($this->security->isGranted('ROLE_USER'))
            return new RedirectResponse($this->router->generate('bloggerblog_blogAdmin'));

//        return new Response("Error");
    }

}