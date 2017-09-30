<?php
/**
 * Created by PhpStorm.
 * User: jerome_skoda
 * Date: 10/05/2016
 * Time: 03:32
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AideController extends Controller
{
    public function showAction($routename = null)
    {
        $aide = null;

        if ($this->container->hasParameter('app_aide')) {
            if (array_key_exists($routename, $this->container->getParameter('app_aide'))) {
                $aide = $this->container->getParameter('app_aide')[$routename];
            }
        }
        return $this->render('AppBundle::aide.html.twig', array('aide' => $aide, 'route' => $routename));
    }

    /**
     * @Route("/aide", name="aide_homepage")
     */
    public function configAction()
    {
        return $this->render('AppBundle::aide_index.html.twig');
    }


}
