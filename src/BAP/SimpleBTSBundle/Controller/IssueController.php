<?php

namespace BAP\SimpleBTSBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Oro\Bundle\SecurityBundle\Annotation\Acl;

use BAP\SimpleBTSBundle\Entity\Issue;

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
     * @return array
     */
    public function indexAction()
    {
        return ['entity_class' => 'BAPSimpleBTSBundle\Entity\Issue'];
    }

    /**
     * @Route("/create", name="bap_bts.issue_create")
     * @Template("BAPSimpleBTSBundle:Issue:update.html.twig")
     * @Acl(
     *     id="bap_bts.issue_create",
     *     type="entity",
     *     class="BAPSimpleBTSBundle:Issue",
     *     permission="CREATE"
     * )
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function createAction(Request $request)
    {
        return $this->update(new Issue(), $request);
    }

    /**
     * @Route("/update/{id}", name="bap_bts.issue_update", requirements={"id":"\d+"}, defaults={"id":0})
     * @Template()
     * @Acl(
     *     id="bap_bts.issue_update",
     *     type="entity",
     *     class="BAPSimpleBTSBundle:Issue",
     *     permission="EDIT"
     * )
     * @param Issue $issue
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function updateAction(Issue $issue, Request $request)
    {
        return $this->update($issue, $request);
    }

    /**
     * @param Issue $issue
     * @param Request $request
     * @return array|RedirectResponse
     */
    private function update(Issue $issue, Request $request)
    {
        $form = $this->get('form.factory')->create('bts_issue', $issue);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->get('oro_ui.router')->redirectAfterSave(
                array(
                    'route' => 'bap_bts.issue_update',
                    'parameters' => array('id' => $issue->getId()),
                ),
                array('route' => 'bap_bts.issue_index'),
                $issue
            );
        }

        return array(
            'entity' => $issue,
            'form' => $form->createView(),
        );
    }
}
