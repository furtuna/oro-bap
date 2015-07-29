<?php

namespace BAP\SimpleBTSBundle\Tests\Entity;

use BAP\SimpleBTSBundle\Entity\IssuePriority;

class IssuePriorityTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $priority = new IssuePriority();
        $name = 'Major';

        $this->assertNull($priority->getName());

        $priority->setName($name);

        $this->assertEquals($name, $priority->getName());
    }

    public function testSortOrder()
    {
        $priority = new IssuePriority();
        $order = 5;

        $this->assertNull($priority->getSortOrder());

        $priority->setSortOrder($order);

        $this->assertEquals($order, $priority->getSortOrder());
    }
}
