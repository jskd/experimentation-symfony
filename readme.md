Installation du projet
----------------------

### Installation des dépendances
L3info_projet2 $ php composer.phar install
L3info_projet2 $ php composer.phar update --prefer-source

### Verifiez les paramettres du serveur
Fichier: L3info_projet2/app/config/parameters.yml

### Déversement des assets
L3info_projet2 $ php app/console assetic:dump

### Importer la structure et les données de la base de donnée:
Fichier: populateBDD.sql

### Mise à jour de la structure
L3info_projet2 $ php app/console doctrine:schema:update --force

Installation d'ElasticShearch
-----------------------------

Les fonctionnalité de recherche utilise une serveur Elasticsearch un moteur de recherche open source.

Pour l'installer sur votre machine, il vous faudra la [ version 1.7.5 ] (https://www.elastic.co/downloads/past-releases/elasticsearch-1-7-5)
et suivre le [guide d'installation](https://www.elastic.co/guide/en/elasticsearch/reference/1.7/_installation.html)

Une fois installé et lancé, veullez verifier dans le fichier: /L3info_projet2/src/RechercheBundle/Resources/config/elastica.yml (le host et le port)

Une fois verifier il faut peupler le serveur de recherche avec la commande

L3info_projet2 $ php app/console fos:elastica:populate
 
Fonctionnalités implémentées
-----------------------------

8 sur 12 modules

Oui Aide
Oui Annuaire
Oui Générateur de fiche de paie
Oui Forum
Oui Fichiers partagés
Oui Moteur de recherche
Oui Statistiques
Oui Tableur
Non Gestion des congées
Non Gestion des formations
Non Gestion des recrutements
Non Un chat interne a l'entreprise 

Utilisateur de test
-------------------

ROLE_USER: login=test-user  mdp=pass
ROLE_SALARIE: login=test-salarie  mdp=pass
ROLE_MODERATOR: login=test-moderator mdp=pass
ROLE_SERVICE_PAIE: test-service_paie mdp=pass
ROLE_ADMIN: test-admin mdp=pass
ROLE_SUPER_ADMIN: test-super_admin  mdp=pass