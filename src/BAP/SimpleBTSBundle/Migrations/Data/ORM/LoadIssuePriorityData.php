<?php

namespace BAP\SimpleBTSBundle\Migrations\Data\ORM;

use BAP\SimpleBTSBundle\Entity\IssuePriority;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIssuePriorityData extends AbstractFixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $priorities = [
            0 => 'Blocker',
            1 => 'Critical',
            2 => 'Minor',
            3 => 'Minimal',
        ];

        foreach ($priorities as $order => $name) {
            $priority = new IssuePriority();
            $priority
                ->setName($name)
                ->setSortOrder($order)
            ;

            $manager->persist($priority);
        }

        $manager->flush();
    }
}
