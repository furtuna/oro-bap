<?php

namespace BAP\SimpleBTSBundle\Form\EventListener;

use BAP\SimpleBTSBundle\Entity\Issue;
use BAP\SimpleBTSBundle\Entity\IssueResolution;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;

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
            FormEvents::PRE_SUBMIT   => 'preSubmit',
            FormEvents::SUBMIT       => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var Issue $issue */
        $issue = $event->getData();
        $hasParent = boolval($issue->getParent());
        $hasChildren = boolval($issue->getChildren()->count());

        $this->changeIssueTypes($event->getForm(), $hasParent, $hasChildren);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $hasParent = ! empty($data['parent']);
        $this->changeIssueTypes($event->getForm(), $hasParent);
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
     * Change issue type choices if has parent/children
     *
     * @param FormInterface $form
     * @param bool $parent
     * @param bool $children
     */
    protected function changeIssueTypes(FormInterface $form, $parent, $children = false)
    {
        if ($form->has('type')) {
            $form->remove('type');
        }

        if ($parent) {
            $choices = [Issue::TYPE_SUBTASK => 'bap.simplebts.issue.type.subtask'];
        } else {
            $choices = [
                Issue::TYPE_STORY   => 'bap.simplebts.issue.type.story',
            ];

            if (! $children) {
                $choices[Issue::TYPE_TASK] = 'bap.simplebts.issue.type.task';
                $choices[Issue::TYPE_BUG]  = 'bap.simplebts.issue.type.bug';
            }
        }

        $form->add('type', 'genemu_jqueryselect2_choice', ['choices' => $choices]);
    }
}
