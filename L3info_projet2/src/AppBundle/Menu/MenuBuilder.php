<?php
/**
 * Created by PhpStorm.
 * User: jerome_skoda
 * Date: 04/05/2016
 * Time: 18:37
 */
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;

class MenuBuilder
{
    private $factory;
    
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav side-nav');
        $menu->addChild('Accueil', array('route' => 'homepage'));
        $menu->addChild('Aide', array('route' => 'aide_homepage'));
        $menu->addChild('Annuaire', array('route' => 'salarie_annuaire'));
        $menu->addChild('Générateur de fiche de paie', array('route' => 'paie_index'));
        $menu->addChild('Forum', array('route' => 'ccdn_homepage'));
        $menu->addChild('Fichiers partagés', array('route' => 'document_index'));
        $menu->addChild('Moteur de recherche', array('route' => 'moteur_recherche'));
        $menu->addChild('Statistiques', array('route' => 'salarie_statistiques'));
        $menu->addChild('Tableur', array('route' => 'tableur'));

        return $menu;
    }
}