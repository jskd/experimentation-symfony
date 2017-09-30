<?php

namespace PaieBundle\Controller;

use PaieBundle\Entity\Cotisation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Elastica\QueryBuilder\DSL\Query;
use Elastica\Suggest\Term;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;

class PaieController extends Controller
{
    /**
     * @Route("/paie", name="paie_index")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function indexAction()
    {
        return $this->render("@Paie/recherche.html.twig");
    }

    /**
     * @Route("/paie/modification/{id}", name="paie_modification")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function updateAction($id)
    {
        $rep = $this->getDoctrine()->getRepository('PaieBundle:Cotisation');
        if ($rep->countCotisationByUserId($id) == 0) {
            return $this->redirectToRoute('paie_initialisation', array('id' => $id));
        }

        $cotisation = $rep->getAllCotisationByUserId($id);

        return $this->render("@Paie/listeCotisation.html.twig", array(
            'cotisations' => $cotisation,
            'brut' => 2470.19
        ));
    }

    /**
     * @Route("/paie/modification-brut/{id}", name="paie_modification_brut")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function updateBrutAction($id, Request $request)
    {
        $brut = $this->getDoctrine()->getRepository('SalarieBundle:Salarie')->find($id);

        if (!$brut) {
            return new Response("Le salarié n'existe pas.", Response::HTTP_NOT_FOUND);
        }

        $form = $this->createFormBuilder($brut)
            ->add('salaire')
            ->add('save', SubmitType::class, array('label' => 'Modifier'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($brut);
            $em->flush();
            return $this->redirectToRoute("paie_modification", array('id' => $brut->getId()));
        }

        return $this->render('@Paie/form.html.twig', array(
            'form' => $form->createView(),
                'title' => "Modification de salaire brut"
        ));
    }

    /**
     * @Route("/paie/cotisation-suppression/{id}", name="cotisation_suppression")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function cotisationSuppressionAction($id, Request $request)
    {
        $cotisation = $this->getDoctrine()->getRepository('PaieBundle:Cotisation')->find($id);

        if (!$cotisation) {
            return new Response("La cotisation n'existe pas.", Response::HTTP_NOT_FOUND);
        }

        $form = $this->createFormBuilder($cotisation)
            ->add('oui', SubmitType::class)
            ->add('non', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('oui')->isClicked()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($cotisation);
                $em->flush();
            }

            return $this->redirectToRoute("paie_modification", array('id' => $cotisation->getSalarie()->getId()));
        }

        return $this->render(
            '@Paie/form.html.twig',
            array(
                'form' => $form->createView(),
                'title' => "Suppression de cotisation",
                'message' => "Voulez-vous vraiment supprimer cette cotisation?"
            )
        );
    }

    /**
     * @Route("/paie/cotisation-modification/{id}", name="cotisation_modification")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function cotisationModificationAction($id, Request $request)
    {
        $cotisation = $this->getDoctrine()->getRepository('PaieBundle:Cotisation')->find($id);

        if (!$cotisation) {
            return new Response("La cotisation n'existe pas.", Response::HTTP_NOT_FOUND);
        }

        $form = $this->createFormBuilder($cotisation)
            ->add('salarialeBase')
            ->add('salarialeTaux')
            ->add('patronaleBase')
            ->add('patronaleTaux')
            ->add('save', SubmitType::class, array('label' => 'Modifier'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cotisation);
            $em->flush();

            return $this->redirectToRoute("paie_modification", array('id' => $cotisation->getSalarie()->getId()));
        }

        return $this->render(
            '@Paie/form.html.twig',
            array(
                'form' => $form->createView(),
                'title' => "Modification de cotisation"
            )
        );
    }


    /**
     * @Route("/paie/cotisation-nouveau/{id}", name="cotisation_nouveau")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function cotisationNouveauAction($id, Request $request)
    {
        $user = $this->getDoctrine()->getRepository('SalarieBundle:Salarie')->find($id);

        if (!$user) {
            return new Response("L'utilisateur n'existe pas.", Response::HTTP_NOT_FOUND);
        }

        $cotisation = new Cotisation();
        $cotisation->setSalarie($user);

        $form = $this->createFormBuilder($cotisation)
            ->add("nom")
            ->add('salarialeBase')
            ->add('salarialeTaux')
            ->add('patronaleBase')
            ->add('patronaleTaux')
            ->add('save', SubmitType::class, array('label' => 'Modifier'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cotisation);
            $em->flush();

            return $this->redirectToRoute("paie_modification", array('id' => $id));
        }

        return $this->render(
            '@Paie/form.html.twig',
            array(
                'form' => $form->createView(),
                'title' => "Nouvelle cotisation"
            )
        );
    }

    /**
     * @Route("/paie/initilisation/{id}", name="paie_initialisation")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function initialisationAction($id, Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('oui', SubmitType::class)
            ->add('non', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('oui')->isClicked()) {
                $this->initializeUserCotization($id);

                return $this->redirectToRoute('paie_modification', array('id' => $id));
            } else {
                return $this->redirectToRoute('paie_index');
            }
        }

        return $this->render(
            '@Paie/form.html.twig',
            array(
                'form' => $form->createView(),
                'title' => "Initialisation de cotisation",
                'message' => "Aucune fiche de paie n'a été généré jusqu'à présent, voulez-vous en initialiser une?"
            )
        );
    }

    public function initializeUserCotization($userId)
    {
        $this->getDoctrine()->getRepository('PaieBundle:Cotisation')->removeAllCotisationByUserId($userId);

        $cotisation_list = $this->container->getParameter('paie_bundle')['default']['cotisation'];

        foreach ($cotisation_list as $elem) {
            foreach ($elem as $cotisation_elem) {
                if (!array_key_exists('nom', $cotisation_elem)) {
                    continue;
                }
                $new_cotisation = new Cotisation();
                $new_cotisation->setNom($cotisation_elem['nom']);
                $new_cotisation->setSalarie(
                    $this->getDoctrine()->getRepository('SalarieBundle:Salarie')->find($userId)
                );
                if (array_key_exists('salariale_base', $cotisation_elem)) {
                    $new_cotisation->setSalarialeBase(floatval(($cotisation_elem['salariale_base'])));
                }
                if (array_key_exists('salariale_taux', $cotisation_elem)) {
                    $new_cotisation->setSalarialeTaux(floatval(($cotisation_elem['salariale_taux'])));
                }
                if (array_key_exists('patronale_base', $cotisation_elem)) {
                    $new_cotisation->setPatronaleBase(floatval(($cotisation_elem['patronale_base'])));
                }
                if (array_key_exists('patronale_taux', $cotisation_elem)) {
                    $new_cotisation->setPatronaleTaux(floatval(($cotisation_elem['patronale_taux'])));
                }
                if (array_key_exists('obligatoire', $cotisation_elem)) {
                    $new_cotisation->setObligatoire(boolval($cotisation_elem['obligatoire']));
                }
                $this->getDoctrine()->getEntityManager()->persist($new_cotisation);
                $this->getDoctrine()->getEntityManager()->flush();
            }
        }
    }


    /**
     * @Route("/paie/recherche/resultat", name="paie_recherche_resultat")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function resultatAction(Request $request)
    {
        $id = $request->request->get("id", 0);
        if ($id == 0) {
            return $this->redirectToRoute('paie_index');
        }

        return $this->redirectToRoute('paie_modification', array('id' => $id));
    }

    /**
     * @Route("/paie/resultat", name="paie_recherche_finder")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function ajaxResponseAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        $searchText = $request->request->get("query", "");
        $searchText = "*".$searchText."*";
        $return = array();
        $return = array_merge($return, $this->searchSalarie($searchText));

        return new JsonResponse(
            $return
        );
    }

    public function searchSalarie($searchText)
    {
        $finder = $this->container->get('fos_elastica.finder.app.salarie');
        $results = $finder->find($searchText);
        $return = array();
        foreach ($results as $value) {
            $return[] = (object)array(
                'type' => 'salarie',
                'label' => (object)array(
                    'nom' => $value->getNom(),
                    'prenom' => $value->getPrenom(),
                    'sexe' => $value->getSexe(),
                    'age' => ((new \DateTime('now'))->diff($value->getDateNaissance())->y),
                ),
                'labelField' => $value->getPrenom()." ".$value->getNom(),
                'searchField' => $value->getPrenom()." ".$value->getNom(),
                'valueField' => $value->getId(),
            );
        }

        return $return;
    }

    public function returnPDFResponseFromHTML($html, $filename)
    {
        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor($this->container->getParameter('paie_bundle')['default']['author']);
        $pdf->SetTitle($this->container->getParameter('paie_bundle')['default']['title']);
        $pdf->SetSubject($this->container->getParameter('paie_bundle')['default']['subject']);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 9, '', true);
        //$pdf->SetMargins(20,20,40, true);
        $pdf->AddPage();

        $pdf->writeHTMLCell(
            $w = 0,
            $h = 0,
            $x = '',
            $y = '',
            $html,
            $border = 0,
            $ln = 1,
            $fill = 0,
            $reseth = true,
            $align = '',
            $autopadding = true
        );
        $pdf->Output($filename.".pdf", 'I'); // This will output the PDF as a response directly
    }


    /**
     * @Route("/paie/bulletin/{id}", name="paie_bulletin")
     * @Security("has_role('ROLE_SERVICE_PAIE')")
     */
    public function index3Action($id)
    {
        $user = $this->getDoctrine()->getRepository('SalarieBundle:Salarie')->find($id);
        if (!$user) {
            return new Response("Le salarié n'existe pas.", Response::HTTP_NOT_FOUND);
        }
        $cotisations = $this->getDoctrine()->getRepository('PaieBundle:Cotisation')->getAllCotisationByUserId($id);

        $html = $this->renderView(
            '@Paie/ficheTemplate.html.twig',
            array(
                'brut' => $user->getSalaire(),
                'cotisations' => $cotisations,
                'user' => $user,
                'entreprise' => $this->container->getParameter('paie_bundle')['default']['entreprise'],
                'employer' => $this->container->getParameter('paie_bundle')['default']['employer'],
            )
        );
        return $this->returnPDFResponseFromHTML($html, "BulletinPaie".$user->getNom().$user->getPrenom());
    }

}
