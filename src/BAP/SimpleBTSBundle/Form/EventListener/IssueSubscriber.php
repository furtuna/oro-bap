<?php

namespace BAP\SimpleBTSBundle\Form\EventListener;

use BAP\SimpleBTSBundle\Entity\Issue;
use BAP\SimpleBTSBundle\Entity\IssueResolution;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IssueSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT       => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $this->addIssueTypes($event);
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        /** @var Issue $issue */
        $issue = $event->getData();

        if ($issue && $issue->getResolution() === null) {
            $defaultResolution = $this->entityManager
                ->getRepository('BAPSimpleBTSBundle:IssueResolution')
                ->findOneBy(['name' => IssueResolution::TYPE_UNRESOLVED])
            ;

            $issue->setResolution($defaultResolution);
        }
    }

    /**
     * @param FormEvent $event
     */
    protected function addIssueTypes(FormEvent $event)
    {
        /** @var Issue $issue */
        $issue = $event->getData();
        $form = $event->getForm();

        if ($issue->getParent()) {
            $choices = [Issue::TYPE_SUBTASK => 'bap.simplebts.issue.type.subtask'];
        } else {
            $choices = [
                Issue::TYPE_STORY   => 'bap.simplebts.issue.type.story',
                Issue::TYPE_TASK    => 'bap.simplebts.issue.type.task',
                Issue::TYPE_BUG     => 'bap.simplebts.issue.type.bug',
            ];
        }

        $form->add('type', 'choice', ['choices' => $choices]);
    }
}
