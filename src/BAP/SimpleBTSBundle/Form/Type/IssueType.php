<?php

namespace BAP\SimpleBTSBundle\Form\Type;

use BAP\SimpleBTSBundle\Entity\Issue;
use BAP\SimpleBTSBundle\Form\EventListener\IssueSubscriber;

use Doctrine\ORM\EntityManager;
use Oro\Bundle\FormBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    /**
     * @var IssueSubscriber
     */
    protected $issueSubscriber;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param IssueSubscriber $issueSubscriber
     * @param EntityManager $entityManager
     */
    public function __construct(
        IssueSubscriber $issueSubscriber,
        EntityManager $entityManager
    ) {
        $this->issueSubscriber = $issueSubscriber;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('summary')
            ->add('description')
            ->add('priority', 'genemu_jqueryselect2_translatable_entity', [
                'class'     => 'BAP\SimpleBTSBundle\Entity\IssuePriority',
                'property'  => 'name',
            ])
            ->add('assignee', 'oro_jqueryselect2_hidden', ['autocomplete_alias' => 'users'])
            ->add('tags', 'oro_tag_select', ['label' => 'oro.tag.entity_plural_label'])
            ->add('parent', 'hidden')
            ->addEventSubscriber($this->issueSubscriber)
        ;

        $issueToIdTransformer = new EntityToIdTransformer($this->entityManager, 'BAPSimpleBTSBundle:Issue');
        $builder->get('parent')->addModelTransformer($issueToIdTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'BAP\SimpleBTSBundle\Entity\Issue',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bts_issue';
    }
}
