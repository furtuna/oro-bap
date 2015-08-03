<?php

namespace BAP\SimpleBTSBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Oro\Bundle\UserBundle\Entity\User;

class IssueRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return array
     */
    public function getUserChartData(User $user)
    {
        return $this->createQueryBuilder('i')
            ->select(
                'workflowStep.label as status',
                'COUNT(i.id) as number'
            )
            ->join('i.workflowStep', 'workflowStep')
            ->groupBy('workflowStep.id')
            ->where('i.assignee = ?1')
            ->setParameter(1, $user)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
