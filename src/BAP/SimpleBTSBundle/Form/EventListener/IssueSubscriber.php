<?php

namespace BAP\SimpleBTSBundle\Form\EventListener;

use BAP\SimpleBTSBundle\Entity\Issue;
use BAP\SimpleBTSBundle\Entity\IssueResolution;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IssueSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityRepository
     */
    protected $issueResolutionRepository;

    /**
     * @param EntityRepository $issueResolutionRepository
     */
    public function __construct(EntityRepository $issueResolutionRepository)
    {
        $this->issueResolutionRepository = $issueResolutionRepository;
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
            /** @var IssueResolution $defaultResolution */
            $defaultResolution = $this->issueResolutionRepository
                ->findOneBy(['code' => IssueResolution::CODE_UNRESOLVED])
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

        $form->add('type', 'genemu_jqueryselect2_choice', ['choices' => $choices]);
    }
}
