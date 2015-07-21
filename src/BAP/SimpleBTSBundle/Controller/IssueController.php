<?php

namespace BAP\SimpleBTSBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Oro\Bundle\SecurityBundle\Annotation\Acl;

/**
 * @Route("/issue")
 */
class IssueController extends Controller
{
    /**
     * @Route("/", name="bap_bts.issue_index")
     * @Template
     * @Acl(
     *     id="bap_bts.issue_view",
     *     type="entity",
     *     class="BAPSimpleBTSBundle:Issue",
     *     permission="VIEW"
     * )
     */
    public function indexAction()
    {
        return ['entity_class' => 'BAPSimpleBTSBundle\Entity\Issue'];
    }
}
