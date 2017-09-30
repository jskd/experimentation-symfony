<?php
/**
 * Created by PhpStorm.
 * User: jerome_skoda
 * Date: 10/05/2016
 * Time: 22:42
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TableurController extends Controller
{
    /**
     * @Route("/tableur", name="tableur")
     */
    public function tableurAction()
    {
        return $this->render('AppBundle::tableur.html.twig');
    }

}