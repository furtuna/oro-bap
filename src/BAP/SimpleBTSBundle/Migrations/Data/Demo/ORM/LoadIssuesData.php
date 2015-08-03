<?php

namespace BAP\SimpleBTSBundle\Migrations\Data\Demo\ORM;

use BAP\SimpleBTSBundle\Entity\Issue;
use BAP\SimpleBTSBundle\Entity\IssuePriority;
use BAP\SimpleBTSBundle\Entity\IssueResolution;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\UserBundle\Entity\User;

class LoadIssuesData extends AbstractFixture implements DependentFixtureInterface
{
    /** @var int */
    protected $tasksCounter = 0;
    /** @var User[] */
    protected $users = [];
    /** @var User */
    protected $mainUser;
    /** @var IssuePriority[] */
    protected $priorities;
    /** @var IssueResolution[] */
    protected $resolutions;
    /** @var OrganizationInterface */
    protected $organization;
    /** @var array */
    protected $mainTaskTypes = [
        Issue::TYPE_STORY,
        Issue::TYPE_BUG,
        Issue::TYPE_TASK,
    ];
    /** @var \DateTime */
    protected $dateStart;
    /** @var \DateTime */
    protected $dateEnd;
    /** @var ObjectManager */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return ['BAP\SimpleBTSBundle\Migrations\Data\Demo\ORM\LoadUsersData'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->mainUser = $this->getReference('bts-main-user');
        for ($i = 1; $i <= LoadUsersData::USERS_COUNT; $i++) {
            $this->users[] = $this->getReference("bts-user{$i}");
        }

        $this->priorities = $manager->getRepository('BAPSimpleBTSBundle:IssuePriority')->findAll();
        $this->resolutions = $manager->getRepository('BAPSimpleBTSBundle:IssueResolution')->findAll();

        $this->organization = $this->mainUser->getOrganization();

        $this->mainTaskTypes = [
            Issue::TYPE_STORY,
            Issue::TYPE_BUG,
            Issue::TYPE_TASK,
        ];

        $this->dateEnd = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->dateStart = clone $this->dateEnd;
        $this->dateStart->modify('-20 days');

        for ($i = 0; $i < 25; $i++) {
            $task = $this->generateTask();
            if ($task->getType() == Issue::TYPE_STORY) {
                $this->generateSubtasks($task);
            }
        }

        $manager->flush();
    }

    /**
     * Generates random task
     *
     * @param string|null $taskType
     * @param Issue|null $parent
     * @return Issue
     */
    protected function generateTask($taskType = null, Issue $parent = null)
    {
        $priority = $this->priorities[array_rand($this->priorities)];
        $resolution = $this->resolutions[array_rand($this->resolutions)];
        $assignee = $this->users[array_rand($this->users)];
        $reporter = $this->users[array_rand($this->users)];

        if ($taskType === null) {
            $taskType = $this->mainTaskTypes[array_rand($this->mainTaskTypes)];
        }

        $taskNumber = sprintf("%'.03d", ++$this->tasksCounter);

        $issue = new Issue();
        $issue
            ->setCode('TT-' . $taskNumber)
            ->setSummary('Test Summary ' . $taskNumber)
            ->setDescription('Lorem ipsum dolor sit amet ' . $taskNumber)
            ->setType($taskType)
            ->setAssignee($assignee)
            ->setReporter($reporter)
            ->setPriority($priority)
            ->setResolution($resolution)
            ->setCreatedAt($this->generateDate())
            ->setUpdatedAt($this->generateDate())
            ->setOwner($reporter)
            ->setOrganization($this->organization)
        ;

        if ($parent !== null) {
            $issue->setParent($parent);
        }

        $this->manager->persist($issue);

        return $issue;
    }

    /**
     * Generates random number of subtasks for story
     *
     * @param Issue $parent
     * @return Issue[]
     */
    protected function generateSubtasks(Issue $parent)
    {
        $tasks = [];
        for ($i = 0; $i < rand(3, 20); $i++) {
            $tasks[] = $this->generateTask(Issue::TYPE_SUBTASK, $parent);
        }

        return $tasks;
    }

    /**
     * Generates random datetime object
     *
     * @return \DateTime
     */
    protected function generateDate()
    {
        $timeStart = $this->dateStart->getTimestamp();
        $timeEnd = $this->dateEnd->getTimestamp();

        $randomTime = rand($timeStart, $timeEnd);
        return \DateTime::createFromFormat('U', $randomTime, new \DateTimeZone('UTC'));
    }
}
