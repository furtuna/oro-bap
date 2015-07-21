<?php

namespace BAP\SimpleBTSBundle\Form\Type;

use BAP\SimpleBTSBundle\Entity\Issue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('summary')
            ->add('description')
            ->add('type', 'choice', [
                'choices' => [
                    Issue::TYPE_STORY   => 'bts.issue.type.story',
                    Issue::TYPE_TASK    => 'bts.issue.type.task',
                    Issue::TYPE_SUBTASK => 'bts.issue.type.subtask',
                    Issue::TYPE_BUG     => 'bts.issue.type.bug',
                ],
            ])
            ->add('priority', 'entity', [
                'class'     => 'BAP\SimpleBTSBundle\Entity\IssuePriority',
                'property'  => 'name',
            ])
            ->add('assignee', 'entity', [
                'class'     => 'Oro\Bundle\UserBundle\Entity\User',
                'property'  => 'username',
            ])
            ->add('tags', 'oro_tag_select', ['label' => 'oro.tag.entity_plural_label'])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BAP\SimpleBTSBundle\Entity\Issue',
        ));
    }

    public function getName()
    {
        return 'bts_issue';
    }
}
