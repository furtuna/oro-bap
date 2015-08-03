<?php

namespace BAP\SimpleBTSBundle\Controller;

use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;

class DashboardController extends Controller
{
    /**
     * @Route("/issue/chart/issue_chart", name="bap_bts.issue_chart")
     * @Template()
     * @Acl(
     *     id="bap_bts.issue_view",
     *     type="entity",
     *     class="BAPSimpleBTSBundle:Issue",
     *     permission="VIEW"
     * )
     */
    public function issueChartAction()
    {
        $items = $this->getDoctrine()
            ->getRepository('BAPSimpleBTSBundle:Issue')
            ->getUserChartData($this->getUser())
        ;

        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig('issue_chart');
        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($items)
            ->setOptions([
                'name' => 'bar_chart',
                'data_schema' => [
                    'label' => ['field_name' => 'status'],
                    'value' => ['field_name' => 'number', 'type' => 'number'],
                ],
                'settings' => ['xNoTicks' => count($items)],
            ])
            ->getView();

        return $widgetAttr;
    }

    /**
     * @Route("/issue/user_issues_grid", name="bap_bts.user_issues_grid")
     * @Template()
     * @Acl(
     *     id="bap_bts.issue_view",
     *     type="entity",
     *     class="BAPSimpleBTSBundle:Issue",
     *     permission="VIEW"
     * )
     */
    public function userIssuesGridAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig('user_issues_grid');
        $widgetAttr['gridName'] = 'user-issues-grid-widget';
        $widgetAttr['params'] = ['userId' => $user->getId()];
        $widgetAttr['renderParams'] = ['enableViews' => false];

        return $widgetAttr;
    }
}
