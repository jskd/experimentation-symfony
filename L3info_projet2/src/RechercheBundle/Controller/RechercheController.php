<?php
/**
 * Created by PhpStorm.
 * User: jerome_skoda
 * Date: 10/05/2016
 * Time: 03:32
 */

namespace RechercheBundle\Controller;

use Elastica\QueryBuilder\DSL\Query;
use Elastica\Suggest\Term;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RechercheController extends Controller
{
    /**
     * @Route("/recherche", name="moteur_recherche")
     */
    public function rechercheAction()
    {
        return $this->render(
            'RechercheBundle::recherche.html.twig',
            array(
                'salarie' => true,
                'document' => true,
                'topic' => true,
                'board' => true,
                'categorie' => true,
            )
        );
    }

    /**
     * @Route("/salarie/annuaire", name="salarie_annuaire")
     */
    public function annuaireAction()
    {
        return $this->render(
            'RechercheBundle::recherche.html.twig',
            array(
                'salarie' => true,
                'document' => false,
                'topic' => false,
                'board' => false,
                'categorie' => false,
            )
        );
    }

    /**
     * @Route("/recherche/redirection", name="moteur_recherche_resultat")
     */
    public function resultatAction(Request $request)
    {
        $url = $request->request->get("url", $this->generateUrl("moteur_recherche"));
        return $this->redirect($url);
    }



    /**
     * @Route("/recherche/resultat", name="moteur_recherche_finder")
     */
    public function ajaxResponseAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        $searchText = $request->request->get("query", "");
        $searchText = "*".$searchText."*";
        $return = array();
        if ($request->request->get("salarie", null) == "true") {
            $return = array_merge($return, $this->searchSalarie($searchText));
        }
        if ($request->request->get("document", null) == "true") {
            $return = array_merge($return, $this->searchDocument($searchText));
        }
        if ($request->request->get("topic", null) == "true") {
            $return = array_merge($return, $this->searchTopic($searchText));
        }
        if ($request->request->get("board", null) == "true") {
            $return = array_merge($return, $this->searchBoard($searchText));
        }
        if ($request->request->get("categorie", null) == "true") {
            $return = array_merge($return, $this->searchCategory($searchText));
        }

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
                'valueField' => $this->generateUrl('salarie_profile', array('id' => $value->getId())),
            );
        }

        return $return;
    }

    public function searchDocument($searchText)
    {
        $query = new \Elastica\Query();
        $q = new \Elastica\Query\QueryString($searchText);
        $term = new \Elastica\Filter\Term(array('publique' => true));
        $filteredQuery = new \Elastica\Query\Filtered($q, $term);
        $query->setQuery($filteredQuery);
        $results = $this->get('fos_elastica.finder.app.document')->find($query);
        $return = array();
        foreach ($results as $value) {
            $return[] = (object)array(
                'type' => 'document',
                'label' => (object)array(
                    'titre' => $value->getTitre(),
                    'description' => $value->getDescription(),
                    'date' => $value->getCreationDate(),
                    'proprietaire' => $value->getProprietaire()->getUsername(),
                    'filename' => $value->getFilename(),
                ),
                'labelField' => $value->getFilename(),
                'searchField' => $value->getTitre()." ".$value->getDescription()." ".$value->getFilename(),
                'valueField' => $this->generateUrl('document_information', array('id' => $value->getId())),
            );
        }

        return $return;
    }

    public function searchTopic($searchText)
    {
        $finder = $this->container->get('fos_elastica.finder.app.forum_topic');
        $results = $finder->find($searchText);
        $return = array();
        foreach ($results as $value) {
            if ($value->getBoard()->isAuthorisedToRead($this->get('security.context'))) {
                if ($value->getBoard()->getCategory()->isAuthorisedToRead($this->get('security.context'))) {
                    $return[] = (object)array(
                        'type' => 'topic',
                        'label' => (object)array(
                            'title' => $value->getTitle(),
                            'view' => $value->getCachedViewCount(),
                            'reply' => $value->getCachedReplyCount(),
                        ),
                        'labelField' => $value->getTitle(),
                        'searchField' => $value->getTitle(),
                        'valueField' => $this->generateUrl(
                            'ccdn_forum_user_topic_show',
                            array('topicId' => $value->getId())
                        ),
                    );
                }
            }
        }

        return $return;
    }

    public function searchBoard($searchText)
    {
        $finder = $this->container->get('fos_elastica.finder.app.forum_board');
        $results = $finder->find($searchText);
        $return = array();
        foreach ($results as $value) {
            if ($value->isAuthorisedToRead($this->get('security.context'))) {
                if ($value->getCategory()->isAuthorisedToRead($this->get('security.context'))) {
                    $return[] = (object)array(
                        'type' => 'board',
                        'label' => (object)array(
                            'name' => $value->getName(),
                            'description' => $value->getDescription(),
                            'topic' => $value->getCachedTopicCount(),
                            'post' => $value->getCachedPostCount(),
                        ),
                        'labelField' => $value->getName(),
                        'searchField' => $value->getName()." ".$value->getDescription(),
                        'valueField' => $this->generateUrl(
                            'ccdn_forum_user_board_show',
                            array('boardId' => $value->getId())
                        ),
                    );
                }
            }
        }

        return $return;
    }

    public function searchCategory($searchText)
    {
        $finder = $this->container->get('fos_elastica.finder.app.forum_category');
        $results = $finder->find($searchText);
        $return = array();
        foreach ($results as $value) {
            if ($value->isAuthorisedToRead($this->get('security.context'))) {
                $return[] = (object)array(
                    'type' => 'category',
                    'label' => (object)array(
                        'name' => $value->getName(),
                    ),
                    'labelField' => $value->getName(),
                    'searchField' => $value->getName(),
                    'valueField' => $this->generateUrl(
                        'ccdn_forum_user_category_show',
                        array('categoryId' => $value->getId())
                    ),
                );
            }
        }

        return $return;
    }
}
