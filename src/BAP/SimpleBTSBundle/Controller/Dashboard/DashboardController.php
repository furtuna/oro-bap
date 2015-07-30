<?php

namespace BAP\SimpleBTSBundle\Controller\Dashboard;

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
    public function issueAction()
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
}
