<?php

namespace BAP\SimpleBTSBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Entity\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsersData extends AbstractFixture implements ContainerAwareInterface
{
    const USERS_COUNT = 5;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->userManager = $container->get('oro_user.manager');
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $usersData = $this->getUsersData();
        $adminUser = $manager->getRepository('OroUserBundle:User')->findOneBy([], ['id' => 'asc']);
        $userRole = $manager->getRepository('OroUserBundle:Role')
            ->findOneBy(['role' => User::ROLE_DEFAULT]);

        if (! $userRole || ! $adminUser) {
            throw new \RuntimeException('User role should exist.');
        }

        foreach ($usersData as $key => $userData) {
            $user = $this->userManager->createUser();

            $user
                ->setUsername($userData['username'])
                ->setEmail($userData['email'])
                ->setEnabled(true)
                ->setOwner($adminUser->getOwner())
                ->setPlainPassword($userData['password'])
                ->addRole($userRole)
                ->setOrganization($adminUser->getOrganization())
                ->addOrganization($adminUser->getOrganization());

            $this->userManager->updateUser($user);
            $this->addReference("bts-user{$key}", $user);
        }

        $this->addReference('bts-main-user', $adminUser);
    }


    /**
     * @return array
     */
    protected function getUsersData()
    {
        $usersData = [];

        for ($i = 1; $i <= self::USERS_COUNT; $i++) {
            $username = "bts_user{$i}_" . substr(md5(uniqid()), 0, 5);

            $usersData[$i] = [
                'username'  => $username,
                'email'     => $username . '@mailinator.com',
                'password'  => '123456',
            ];
        }

        return $usersData;
    }
}
