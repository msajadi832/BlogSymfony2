<?php
namespace blogger\blogBundle\EventListener;

use blogger\blogBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegisterListener implements EventSubscriberInterface
{
    private $em;
    private $router;

    public function __construct(EntityManager $em, UrlGeneratorInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegister',
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegisterCompleted',
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'onChangePassCompleted',
        );
    }

    public function onRegister(GetResponseUserEvent $event)
    {
        $admin = $this->em->getRepository('bloggerblogBundle:User')->find(6);

        /** @var User $user */
        $user = $event->getUser();
        $user->setBlogTemplate($admin->getBlogTemplate());
    }

    public function onRegisterCompleted(FilterUserResponseEvent $response)
    {
        $url = $this->router->generate('bloggerblog_blogAdminShowRecentComments', array('articleId' => 'all'));
        $response->getResponse()->headers->set('Location',$url);
    }

    public function onChangePassCompleted(FilterUserResponseEvent $response)
    {
        $response->getRequest()->getSession()->getFlashBag()->add('adminSuccess', 'رمز عبور با موفقیت ویرایش شد.');
        $url = $this->router->generate('fos_user_change_password');
        $response->getResponse()->headers->set('Location',$url);
    }
}