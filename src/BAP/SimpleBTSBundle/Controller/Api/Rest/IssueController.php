<?php

namespace BAP\SimpleBTSBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;

use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("issue")
 * @NamePrefix("bap_bts_api_")
 */
class IssueController extends RestController
{
    /**
     * @Acl(
     *      id="bap_bts.issue_delete",
     *      type="entity",
     *      class="BAPSimpleBTSBundle:Issue",
     *      permission="DELETE"
     * )
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFormHandler()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->get('bap_bts.issue_manager.api');
    }
}
