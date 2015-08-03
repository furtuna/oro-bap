<?php

namespace BAP\SimpleBTSBundle\Provider;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Entity\UserManager;

class IssuesGridHelper
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return array
     */
    public function getUsersChoices()
    {
        /** @var User[] $users */
        $users = $this->userManager->findUsers();
        $choices = [];
        foreach ($users as $user) {
            $choices[$user->getId()] = $user->getUsername();
        }

        return $choices;
    }
}
