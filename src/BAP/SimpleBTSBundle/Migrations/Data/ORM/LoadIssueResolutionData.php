<?php

namespace BAP\SimpleBTSBundle\Migrations\Data\ORM;

use BAP\SimpleBTSBundle\Entity\IssueResolution;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;

class LoadIssueResolutionData extends AbstractTranslatableEntityFixture
{
    const RESOLUTION_PREFIX = 'issue_resolution';

    /**
     * {@inheritdoc}
     */
    protected function loadEntities(ObjectManager $manager)
    {
        $resolutions = [
            'unresolved',
            'fixed',
            'wont_fix',
            'duplicate',
            'incomplete',
            'cant_reproduce',
            'done',
            'wont_do',
        ];

        $resolutionRepository = $manager->getRepository('BAPSimpleBTSBundle:IssueResolution');
        $translationLocales = $this->getTranslationLocales();

        foreach ($translationLocales as $locale) {
            foreach ($resolutions as $code) {
                /** @var IssueResolution $resolution */
                $resolution = $resolutionRepository->findOneBy(['code' => $code]);
                if (! $resolution) {
                    $resolution = new IssueResolution();
                    $resolution->setCode($code);
                }

                $name = $this->translate($code, static::RESOLUTION_PREFIX, $locale);
                $resolution
                    ->setLocale($locale)
                    ->setName($name)
                ;

                $manager->persist($resolution);
            }

            $manager->flush();
        }
    }
}
