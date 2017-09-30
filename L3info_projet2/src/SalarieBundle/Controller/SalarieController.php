<?php

namespace SalarieBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SalarieController extends Controller
{
    /**
     * @Route("/salarie/statistiques", name="salarie_statistiques")
     */
    public function generationStatistiquesAction()
    {
        return $this->render('@Salarie/statistique.html.twig');
    }

    /**
     * @Route("/salarie/profile/{id}", requirements={"id" = "\d+"}, name="salarie_profile")
     */
    public function profileAction($id)
    {
        $salarie = $this->getDoctrine()
            ->getRepository('SalarieBundle:Salarie')
            ->find($id);

        if (!$salarie) {
            throw $this->createNotFoundException(
                'No salarie found for id '.$id
            );
        }

        return $this->render('@Salarie/profile.html.twig', array('salarie' => $salarie));
    }
    /**
     * @Route("/salarie/profile/post", name="salarie_profile_post")
     */
    public function profilePostAction(Request $request)
    {
        $id = $request->request->get("id", null);
        if (!$id) {
            return new Response('Champs id manquant');
        }

        return $this->redirectToRoute('salarie_profile', array('id' => $id));
    }

    /**
     * @Route("/salarie/statistiques_generator", name="salarie_statistiques_generator")
     */
    public function staticticResponse(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        $condition = $request->request->get("condition", null);
        $param = $request->request->get("params", null);
        $condition = $this->doctrineCondition($condition);
        $choice = $request->request->get("chart-type", null);
        switch ($choice) {
            case "0":
                $chart = $this->_getSexeAgeChat($condition, $param);
                break;
            case "1":
                $chart = $this->_getContratChat($condition, $param);
                break;
            case "2":
                $chart = $this->_getCountCDIChat($condition, $param);
                break;
            default:
                return new JsonResponse(array('message' => 'Error invalid "caractéristique étudié"'), 400);
                break;
        }
        return new JsonResponse(
            array(
                'chart' => $chart,
            )
        );
    }

    private function doctrineCondition($condition)
    {
        $field = array(
            'id',
            'nom',
            'prenom',
            'dateNaissance',
            'sexe',
            'dateEntre',
            'typeContrat',
            'DureeContrat',
            'salaire',
            'superieurHierarchique',
        );
        foreach ($field as $value) {
            $re[] = "/(?<!:)(".$value.")(?!_)/";
            $replacement[] = 'a.${1}';
        }
        $condition = preg_replace($re, $replacement, $condition);
        return $condition;
    }


    private function _getContratChat($condition = null, $param = null)
    {
        $cols = array(
            array(
                'field' => 'CDI',
                'label' => 'CDI',
            ),
            array(
                'field' => 'CDD',
                'label' => 'CDD',
            ),
            array(
                'field' => 'sta',
                'label' => 'Stagiaire',
            ),
            array(
                'field' => 'vol',
                'label' => 'Volontaire',
            ),
        );
        $data = array();
        $label = array();
        foreach ($cols as $col) {
            $data[] = $this->getDoctrine()->getRepository('SalarieBundle:Salarie')->getContratCount(
                $col['field'],
                $condition,
                $param
            );
            $label[] = $col['label'];
        }
        $dateSet = array(
            (object)array(
                'data' => $data,
                'backgroundColor' => array("#FF6384", "#4BC0C0", "#FFCE56", "#36A2EB"),
                'hoverBackgroundColor' => array("#FF6384", "#4BC0C0", "#FFCE56", "#36A2EB"),
            ),
        );
        return (object)array(
            'type' => 'pie',
            'data' => (object)array(
                'labels' => $label,
                'datasets' => $dateSet,
            ),
        );
    }


    private function _getSexeAgeChat($condition = null, $param = null)
    {
        $cols = array(
            array(
                'label' => 'Femme',
                'sexe' => 'F',
                'backgroundColor' => "rgba(255,99,132,0.2)",
                'borderColor' => "rgba(255,99,132,1)",
                'hoverBackgroundColor' => "rgba(255,99,132,0.4)",
                'hoverBorderColor' => "rgba(255,99,132,1)",
            ),
            array(
                'label' => 'Homme',
                'sexe' => 'M',
                'backgroundColor' => "rgba(99,132,255,0.2)",
                'borderColor' => "rgba(99,132,255,1)",
                'hoverBackgroundColor' => "rgba(99,132,255,0.4)",
                'hoverBorderColor' => "rgba(99,132,255,1)",
            ),
        );
        foreach ($cols as $col) {
            $data = array();
            for ($age_min = 15; $age_min < 70; $age_min += 5) {
                $value = $this->getDoctrine()->getRepository('SalarieBundle:Salarie')->getCountCDIbySexeAndAgeRange(
                    $col['sexe'],
                    $age_min,
                    $age_min + 5,
                    $condition,
                    $param
                );
                $data[] = $value;
            }
            $dateSet[] = (object)array(
                'label' => $col['label'],
                'backgroundColor' => $col['backgroundColor'],
                'borderColor' => $col['borderColor'],
                'borderWidth' => 1,
                'hoverBackgroundColor' => $col['hoverBackgroundColor'],
                'hoverBorderColor' => $col['hoverBorderColor'],
                'data' => $data,
            );
        }
        for ($age_min = 15; $age_min < 70; $age_min += 5) {
            $label[] = ($age_min)."-".($age_min + 5);
        }
        return (object)array(
            'type' => 'bar',
            'data' => (object)array(
                'labels' => $label,
                'datasets' => $dateSet,
            ),
        );
    }

    private function _getCountCDIChat($condition = null, $param = null)
    {
        for ($i = 1975; $i < 2016; $i++) {
            $value = $this->getDoctrine()->getRepository('SalarieBundle:Salarie')->getCountCDIbyYear(
                $i,
                $condition,
                $param
            );
            $data[] = $value;
            $label[] = $i;
        }
        return (object)array(
            'type' => 'line',
            'data' => (object)array(
                'labels' => $label,
                'datasets' => array(
                    (object)array(
                        'label' => "Nombre de CDI",
                        'data' => $data,
                    ),
                ),
            ),
            'options' => (object)array(
                'scales' => (object)array(
                    'yAxes' => array(
                        (object)array(
                            'ticks' => (object)array(
                                'beginAtZero' => true,
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}
