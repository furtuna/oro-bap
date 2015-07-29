<?php

namespace BAP\SimpleBTSBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @Route("/{id}", name="bap_bts.issue_view", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *     id="bap_bts.issue_view",
     *     type="entity",
     *     class="BAPSimpleBTSBundle:Issue",
     *     permission="VIEW"
     * )
     * @param Issue $issue
     * @return array
     */
    public function viewAction(Issue $issue)
    {
        return ['entity' => $issue];
    }

    /**
     * @Route(
     *     "/create/{parent_id}",
     *     name="bap_bts.issue_create",
     *     requirements={"parent_id":"\d+"},
     *     defaults={"parent_id":0}
     * )
     * @ParamConverter("parent", class="BAPSimpleBTSBundle:Issue", options={"id":"parent_id"})
     * @Template("BAPSimpleBTSBundle:Issue:update.html.twig")
     * @Acl(
     *     id="bap_bts.issue_create",
     *     type="entity",
     *     class="BAPSimpleBTSBundle:Issue",
     *     permission="CREATE"
     * )
     * @param Request $request
     * @param Issue $parent
     * @return array|RedirectResponse
     */
    public function createAction(Request $request, Issue $parent = null)
    {
        $issue = new Issue();
        if ($parent) {
            $issue->setParent($parent);
        }

        return $this->update($issue, $request);
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
     * TODO: Create form handler
     *
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
            $this->get('oro_tag.tag.manager')->saveTagging($issue);

            if ($issue->getParent()) {
                $redirect = [
                    'route'      => 'bap_bts.issue_view',
                    'parameters' => ['id' => $issue->getParent()->getId()]
                ];
            } else {
                $redirect = ['route' => 'bap_bts.issue_index'];
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('bap.simplebts.controller.issue.saved')
            );

            return $this->get('oro_ui.router')->redirectAfterSave(
                [
                    'route' => 'bap_bts.issue_update',
                    'parameters' => ['id' => $issue->getId()],
                ],
                $redirect,
                $issue
            );
        }

        return [
            'entity' => $issue,
            'form' => $form->createView(),
        ];
    }
}
