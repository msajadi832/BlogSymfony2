<?php
namespace blogger\blogBundle\EventListener;

use blogger\blogBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegisterListener implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegister',
        );
    }

    public function onRegister(GetResponseUserEvent $event)
    {
        $admin = $this->em->getRepository('bloggerblogBundle:User')->find(6);

        /** @var User $user */
        $user = $event->getUser();
        $user->setBlogTemplate($admin->getBlogTemplate());
    }
}