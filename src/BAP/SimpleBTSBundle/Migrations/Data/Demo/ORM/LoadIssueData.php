<?php

namespace BAP\SimpleBTSBundle\Migrations\Data\Demo\ORM;

use BAP\SimpleBTSBundle\Entity\Issue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\UserBundle\Entity\User;

class LoadIssueData extends AbstractFixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var ArrayCollection|User[] $users */
        $users = $manager->getRepository('OroUserBundle:User')->findBy([], ['id' => 'asc'], 1);
        $priorities = $manager->getRepository('BAPSimpleBTSBundle:IssuePriority')->findAll();
        $resolutions = $manager->getRepository('BAPSimpleBTSBundle:IssueResolution')->findAll();

        if (count($users) == 0) {
            return;
        }

        $user = $users[0];

        for ($i = 0; $i < 100; $i++) {
            $priority = $priorities[array_rand($priorities)];
            $resolution = $resolutions[array_rand($resolutions)];

            $issue = new Issue();
            $issue
                ->setCode('TT-' . sprintf("%'.04d", $i))
                ->setSummary('Test Summary ' . $i)
                ->setType(Issue::TYPE_TASK)
                ->setDescription(str_repeat('Lorem ipsum dolor sit amet... ', 10))
                ->setAssignee($user)
                ->setReporter($user)
                ->setOrganization($user->getOrganization())
                ->setPriority($priority)
                ->setResolution($resolution)
            ;

            $manager->persist($issue);
        }

        $manager->flush();
    }
}
