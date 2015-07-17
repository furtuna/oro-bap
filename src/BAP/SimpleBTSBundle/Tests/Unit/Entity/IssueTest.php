<?php

namespace BAP\SimpleBTSBundle\Tests\Entity;

use BAP\SimpleBTSBundle\Entity\Issue;
use BAP\SimpleBTSBundle\Entity\IssuePriority;
use BAP\SimpleBTSBundle\Entity\IssueResolution;
use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\TagBundle\Entity\Tag;
use Oro\Bundle\UserBundle\Entity\User;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    public function testSummary()
    {
        $issue = new Issue();
        $summary = 'Test Summary';

        $this->assertNull($issue->getSummary());

        $issue->setSummary($summary);

        $this->assertEquals($summary, $issue->getSummary());
    }

    public function testCode()
    {
        $issue = new Issue();
        $code = 'TTT-0005';

        $this->assertNull($issue->getCode());

        $issue->setCode($code);

        $this->assertEquals($code, $issue->getCode());
    }

    public function testDescription()
    {
        $issue = new Issue();
        $description = 'Test Description';

        $this->assertNull($issue->getDescription());

        $issue->setDescription($description);

        $this->assertEquals($description, $issue->getDescription());
    }

    public function testType()
    {
        $issue = new Issue();

        $this->assertNull($issue->getType());

        $issue->setType(Issue::TYPE_STORY);

        $this->assertEquals(Issue::TYPE_STORY, $issue->getType());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTypeException()
    {
        $issue = new Issue();
        $issue->setType('Unknown type');
    }

    public function testTags()
    {
        $issue = new Issue();
        $tags = new ArrayCollection();
        $tags->add(new Tag());
        $tags->add(new Tag());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $issue->getTags());

        $issue->setTags($tags);

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $issue->getTags());
        $this->assertCount(2, $issue->getTags());
    }

    public function testAssignee()
    {
        $issue = new Issue();
        $user = new User();

        $this->assertNull($issue->getAssignee());

        $issue->setAssignee($user);

        $this->assertEquals($user, $issue->getAssignee());
    }

    public function testReporter()
    {
        $issue = new Issue();
        $user = new User();

        $this->assertNull($issue->getReporter());

        $issue->setReporter($user);

        $this->assertEquals($user, $issue->getReporter());
    }

    public function testCreatedAtUpdatedAt()
    {
        $issue = new Issue();
        $created = new \DateTime('now', new \DateTimeZone('UTC'));
        $updated = new \DateTime('now', new \DateTimeZone('UTC'));

        $issue->setUpdatedAt($updated);
        $issue->setCreatedAt($created);

        $this->assertEquals($updated, $issue->getUpdatedAt());
        $this->assertEquals($created, $issue->getCreatedAt());
    }

    public function testRelatedIssues()
    {
        $issue = new Issue();
        $relatedIssue1 = new Issue();
        $relatedIssue2 = new Issue();

        $issue->addRelatedIssue($relatedIssue1);
        $issue->addRelatedIssue($relatedIssue2);

        $relatedIssue1->addRelatedToIssue($issue);

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $issue->getRelatedIssues());
        $this->assertCount(2, $issue->getRelatedIssues());
        $this->assertContains($relatedIssue1, $issue->getRelatedIssues());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $relatedIssue1->getRelatedToIssues());
        $this->assertCount(1, $relatedIssue1->getRelatedToIssues());
        $this->assertContains($issue, $relatedIssue1->getRelatedToIssues());

        $issue->removeRelatedIssue($relatedIssue1);
        $relatedIssue1->removeRelatedToIssue($issue);

        $this->assertCount(0, $relatedIssue1->getRelatedToIssues());
        $this->assertCount(1, $issue->getRelatedIssues());
        $this->assertContains($relatedIssue2, $issue->getRelatedIssues());
    }

    public function testCollaborators()
    {
        $issue = new Issue();
        $user1 = new User();
        $user2 = new User();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $issue->getCollaborators());
        $this->assertCount(0, $issue->getCollaborators());

        $issue->addCollaborator($user1);
        $issue->addCollaborator($user2);

        $this->assertCount(2, $issue->getCollaborators());
        $this->assertContains($user1, $issue->getCollaborators());

        $issue->removeCollaborator($user1);

        $this->assertCount(1, $issue->getCollaborators());
        $this->assertContains($user2, $issue->getCollaborators());
    }

    public function testParent()
    {
        $issue = new Issue();
        $parent = new Issue();

        $this->assertNull($issue->getParent());

        $issue->setParent($parent);

        $this->assertEquals($parent, $issue->getParent());
    }

    public function testChildren()
    {
        $issue = new Issue();
        $child1 = new Issue();
        $child2 = new Issue();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $issue->getChildren());

        $issue->addChild($child1);
        $issue->addChild($child2);

        $this->assertCount(2, $issue->getChildren());

        $issue->removeChild($child1);

        $this->assertCount(1, $issue->getChildren());
        $this->assertContains($child2, $issue->getChildren());
    }

    public function testPriority()
    {
        $issue = new Issue();
        $priority = new IssuePriority();

        $this->assertNull($issue->getPriority());

        $issue->setPriority($priority);

        $this->assertEquals($priority, $issue->getPriority());
    }

    public function testResolution()
    {
        $issue = new Issue();
        $resolution = new IssueResolution();

        $this->assertNull($issue->getResolution());

        $issue->setResolution($resolution);

        $this->assertEquals($resolution, $issue->getResolution());
    }
}
