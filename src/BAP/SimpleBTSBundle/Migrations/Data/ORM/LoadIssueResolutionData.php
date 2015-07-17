<?php
namespace BAP\SimpleBTSBundle\Migrations\Data\ORM;

use BAP\SimpleBTSBundle\Entity\IssueResolution;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIssueResolutionData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $resolutions = [
            'Fixed',
            'Won\'t Fix',
            'Duplicate',
            'Incomplete',
            'Cannot Reproduce',
            'Done',
            'Won\'t Do',
        ];

        foreach ($resolutions as $name) {
            $resolution = new IssueResolution();
            $resolution->setName($name);
            $manager->persist($resolution);
        }

        $manager->flush();
    }
}
