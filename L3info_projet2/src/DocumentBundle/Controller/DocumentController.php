<?php

namespace DocumentBundle\Controller;

use DocumentBundle\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/document/nouveau", name="document_nouveau")
     */
    public function newAction(Request $request)
    {
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('titre', TextType::class)
            ->add('description', TextType::class)
            ->add('file')
            ->add('publique')
            ->add('save', SubmitType::class, array('label' => 'Créer'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $document->setProprietaire($this->getUser());
            $document->upload($this->get('kernel')->getRootDir().'/../web/uploads/');
            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute("document_index");
        }

        return $this->render(
            '@Document/form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/document/modification/{id}", name="document_modification", requirements={"id" = "\d+"})
     */
    public function updateAction($id, Request $request)
    {
        $document = $this->getDoctrine()
            ->getRepository('DocumentBundle:Document')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->setMaxResults(1)->getOneOrNullResult();

        if (!$document) {
            return new Response("Le document n'existe pas.", Response::HTTP_NOT_FOUND);
        }

        if ($document->getProprietaire() != $this->getUser()) {
            return new Response("Vous n'êtes pas le propriétaire du document.", Response::HTTP_UNAUTHORIZED);
        }

        $form = $this->createFormBuilder($document)
            ->add('titre', TextType::class)
            ->add('description', TextType::class)
            ->add('publique')
            ->add('save', SubmitType::class, array('label' => 'Modifier'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirectToRoute("document_index");
        }

        return $this->render(
            '@Document/form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/document/suppression/{id}", name="document_suppression", requirements={"id" = "\d+"})
     */
    public function suppressionAction($id, Request $request)
    {
        $document = $this->getDoctrine()
            ->getRepository('DocumentBundle:Document')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->setMaxResults(1)->getOneOrNullResult();

        if (!$document) {
            return new Response("Le document n'existe pas.", Response::HTTP_NOT_FOUND);
        }

        if ($document->getProprietaire() != $this->getUser()) {
            return new Response("Vous n'êtes pas le propriétaire du document.", Response::HTTP_UNAUTHORIZED);
        }

        $form = $this->createFormBuilder($document)
            ->add('save', SubmitType::class, array('label' => 'Confirmer'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();

            return $this->redirectToRoute("document_index");
        }

        return $this->render(
            '@Document/form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

    }

    /**
     * @Route("/document", name="document_index")
     */
    public function indexAction()
    {
        return $this->render(
            '@Document/liste.html.twig'
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/document/liste/prive", name="document_liste_prive")
     */
    public function listePriveAction()
    {
        $documents_query = $this->getDoctrine()
            ->getRepository('DocumentBundle:Document')
            ->createQueryBuilder('d')
            ->select('d.id, d.titre, d.creation_date, d.filename, d.clef, d.publique')
            ->orderBy('d.creation_date', 'desc')
            ->where('d.proprietaire = :proprietaire')
            ->setParameter('proprietaire', $this->getUser())
            ->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $documents_query,
            $this->get('request')->query->getInt('page', 1),
            10
        );

        return $this->render(
            '@Document/listePrive.html.twig',
            array('pagination' => $pagination, 'nom' => $this->getRequest()->getBasePath().'/uploads/')
        );
    }

    /**
     * @Route("/document/liste/publique", name="document_liste_publique")
     */
    public function listePublicAction()
    {
        $documents_query = $this->getDoctrine()->getRepository('DocumentBundle:Document')
            ->createQueryBuilder('d')
            ->where('d.publique = :publique')
            ->setParameter('publique', true)
            ->orderBy('d.id', 'DESC')
            ->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination_publique = $paginator->paginate(
            $documents_query,
            $this->get('request')->query->getInt('page', 1),
            10
        );

        return $this->render(
            '@Document/listePublique.html.twig',
            array(
                'pagination' => $pagination_publique,
                'nom' => $this->getRequest()->getBasePath().'/uploads/',
            )
        );
    }

    /**
     * @Route("/document/telechargement/{id}/{key}", name="document_telechargement", requirements={"id" = "\d+"}, defaults={"key" = null})
     */

    public function downloadAction($id, $key)
    {
        $document = $this->getDoctrine()
            ->getRepository('DocumentBundle:Document')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->setMaxResults(1)->getOneOrNullResult();

        if ($document == null) {
            return new Response("Le document n'existe pas", Response::HTTP_NOT_FOUND);
        }

        if (!($document->getPublique() == true || ($document->getPublique() == false && $document->getClef(
                ) == $key))
        ) {
            return new Response("Accès au document non autorisé", Response::HTTP_UNAUTHORIZED);
        }

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($document->getPath()));
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$document->getFilename().'";');
        $response->headers->set('Content-length', filesize($document->getPath()));
        $response->setContent(file_get_contents($document->getPath()));

        return $response;
    }


    /**
     * @Route("/document/information/{id}/{key}", name="document_information", requirements={"id" = "\d+"}, defaults={"key" = null})
     */

    public function informationAction($id, $key)
    {
        $document = $this->getDoctrine()
            ->getRepository('DocumentBundle:Document')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->setMaxResults(1)->getOneOrNullResult();


        if ($document == null) {
            return new Response("Le document n'existe pas", Response::HTTP_NOT_FOUND);
        }

        if (!($document->getPublique() == true || ($document->getPublique() == false && $document->getClef(
                ) == $key))
        ) {
            return new Response("Accès au document non autorisé", Response::HTTP_UNAUTHORIZED);
        }

        return $this->render(
            '@Document/information.html.twig',
            array('document' => $document, 'clef' => $key)
        );
    }
}
