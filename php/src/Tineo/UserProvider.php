<?php

namespace Tineo;


use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;


class UserProvider implements UserProviderInterface
{
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function loadUserByUsername($username)
    {
        $em = $this->app["orm.em"];
        if($em instanceof \Doctrine\ORM\EntityManager){

            $query = $em->createQuery("SELECT u FROM Tineo\Entity\User u WHERE u.username = :username");
            $query->setParameter('username', $username);
            if(!$user = $query->getResult() ){
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            }
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof \Tineo\Entity\User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === '\Tineo\Entity\User';
    }
}