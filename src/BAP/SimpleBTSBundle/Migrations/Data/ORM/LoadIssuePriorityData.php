<?php

namespace BAP\SimpleBTSBundle\Migrations\Data\ORM;

use BAP\SimpleBTSBundle\Entity\IssuePriority;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;

class LoadIssuePriorityData extends AbstractTranslatableEntityFixture
{
    const PRIORITY_PREFIX = 'issue_priority';

    /**
     * {@inheritdoc}
     */
    protected function loadEntities(ObjectManager $manager)
    {
        $priorities = [
            0 => 'blocker',
            1 => 'critical',
            2 => 'minor',
            3 => 'minimal',
        ];

        $priorityRepository = $manager->getRepository('BAPSimpleBTSBundle:IssuePriority');
        $translationLocales = $this->getTranslationLocales();

        foreach ($translationLocales as $locale) {
            foreach ($priorities as $order => $code) {
                /** @var IssuePriority $priority */
                $priority = $priorityRepository->findOneBy(['code' => $code]);
                if (! $priority) {
                    $priority = new IssuePriority();
                    $priority
                        ->setCode($code)
                        ->setSortOrder($order)
                    ;
                }

                $name = $this->translate($code, static::PRIORITY_PREFIX, $locale);
                $priority
                    ->setLocale($locale)
                    ->setName($name)
                ;

                $manager->persist($priority);
            }

            $manager->flush();
        }
    }
}
