<?php

namespace BAP\SimpleBTSBundle\Tests\Functional;

use BAP\SimpleBTSBundle\Entity\Issue;
use BAP\SimpleBTSBundle\Entity\Repository\IssueRepository;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\DomCrawler\Form;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class IssueControllerTest extends WebTestCase
{
    /**
     * @var IssueRepository
     */
    protected static $issueRepository;

    /**
     * @var User
     */
    protected static $user;

    public static function setUpBeforeClass()
    {
        $em = self::getContainer()->get('doctrine');
        self::$issueRepository = $em->getRepository('BAPSimpleBTSBundle:Issue');
        self::$user = self::getContainer()->get('oro_user.manager')->findUserByUsername('admin');
    }

    public static function tearDownAfterClass()
    {
        self::$issueRepository = null;
        self::$user = null;
    }

    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('bap_bts.issue_index'));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    /**
     * @depends testIndex
     */
    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('bap_bts.issue_create'));

        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['bts_issue[code]'] = 'FT-001';
        $form['bts_issue[summary]'] = 'Summary 001';
        $form['bts_issue[description]'] = 'Description 001';
        $form['bts_issue[type]'] = Issue::TYPE_STORY;
        $form['bts_issue[assignee]'] = self::$user->getId();

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue saved', $crawler->html());
    }

    /**
     * @depends testCreate
     */
    public function testCreateSubtask()
    {
        /** @var Issue $parentIssue */
        $parentIssue = self::$issueRepository->findBy(['code' => 'FT-001']);

        $crawler = $this->client->request('GET', $this->getUrl(
            'bap_bts.issue_create',
            ['parent_id' => $parentIssue->getId()]
        ));

        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['bts_issue[code]'] = 'FT-002';
        $form['bts_issue[summary]'] = 'Summary 002';
        $form['bts_issue[description]'] = 'Description 002';
        $form['bts_issue[type]'] = Issue::TYPE_SUBTASK;
        $form['bts_issue[assignee]'] = self::$user->getId();

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue saved', $crawler->html());
    }

    /**
     * @depends testCreateSubtask
     */
    public function testIssuesGrid()
    {
        $response = $this->client->requestGrid('issues-grid');
        $result = $this->getJsonResponseContent($response, 200);
        $this->assertEquals(2, $result['options']['totalRecords']);
    }
}
