<?php

namespace BAP\SimpleBTSBundle\Tests\Entity;

use BAP\SimpleBTSBundle\Entity\IssueResolution;

class IssueResolutionTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $resolution = new IssueResolution();
        $name = 'Fixed';

        $this->assertNull($resolution->getName());

        $resolution->setName($name);

        $this->assertEquals($name, $resolution->getName());
    }
}
