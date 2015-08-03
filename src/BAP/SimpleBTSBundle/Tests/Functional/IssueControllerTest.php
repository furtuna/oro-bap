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
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->getContainer()->get('oro_user.manager')->findUserByUsername('admin');
    }

    /**
     * @return IssueRepository
     */
    protected function getIssueRepository()
    {
        return $this->getContainer()->get('doctrine')->getRepository('BAPSimpleBTSBundle:Issue');
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
        $user = $this->getUser();
        $crawler = $this->client->request('GET', $this->getUrl('bap_bts.issue_create'));

        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['bts_issue[code]'] = 'FT-001';
        $form['bts_issue[summary]'] = 'Summary 001';
        $form['bts_issue[description]'] = 'Description 001';
        $form['bts_issue[type]'] = Issue::TYPE_STORY;
        $form['bts_issue[assignee]'] = $user->getId();

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
        $user = $this->getUser();
        /** @var Issue $parentIssue */
        $parentIssue = $this->getIssueRepository()->findOneBy(['code' => 'FT-001']);

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
        $form['bts_issue[assignee]'] = $user->getId();
        $form['bts_issue[parent]'] = $parentIssue->getId();

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

    /**
     * @depends testIssuesGrid
     */
    public function testSubtasksGrid()
    {
        /** @var Issue $parentIssue */
        $parentIssue = $this->getIssueRepository()->findOneBy(['code' => 'FT-001']);

        $response = $this->client->requestGrid('issue-subtasks-grid', [
            'issue-subtasks-grid[parentIssue]' => $parentIssue->getId(),
        ]);

        $result = $this->getJsonResponseContent($response, 200);
        $this->assertEquals(1, $result['options']['totalRecords']);
    }

    /**
     * @depends testSubtasksGrid
     */
    public function testUpdate()
    {
        /** @var Issue $issue */
        $issue = $this->getIssueRepository()->findOneBy(['code' => 'FT-001']);

        $crawler = $this->client->request('GET', $this->getUrl(
            'bap_bts.issue_update',
            ['id' => $issue->getId()]
        ));

        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['bts_issue[summary]'] = 'Summary 001 changed';
        $form['bts_issue[description]'] = 'Description 001 changed';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue saved', $crawler->html());
    }
}
