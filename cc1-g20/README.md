Baptiste Canault, baptiste.canault1@etu.univ-orleans.fr
Jeremy Carre, jeremy.carre1@etu.univ-orleans.fr
Theo Bourgeois, theo.bourgeois@etu.univ-orleans.fr
Anthony Grosmaire, anthony.grosmaire@etu.univ-orleans.fr

Q1)
- Dans une invite de commande à l'endroit où on veut que le projet soit :
    docker compose build
    docker compose up -d
- Ensuite en ses connectant au container du projet :
    symfony new medico --webapp
- On se déplace dans medico dans le container et on démarre le serveur :
    symfony server:start --allow-all-ip -d
    Le serveur de base est à l'adresse : http://localhost:8088/

Q2)
- Pour créer le controlleur pour la page about j'ai fais la commande :
    symfony console make:controller AboutController

Q3)
- Pour créer le modèle consultation j'ai fais les commandes :
    symfony console make:entity Consultation
    symfony console doctrine:database:create
- J'ai ensuite fais la migration de la base de donnée :
    symfony console make:migration
    symfony console doctrine:migration:migrate

Q4)
- Pour créer les 50 fixtures et installer faker j'ai fais les commande :
    symfony composer require --dev doctrine/doctrine-fixtures-bundle
    symfony composer require --dev orm-fixtures fakerphp/faker
- J'ai ensuite crée la fixture consultation avec la commande :
    symfony console make:fixture ConsultationFixture
- J'ai ensuite chargé les données générées avec la commande :
    symfony console doctrine:fixture:load

Q5-9)
- Pour créer le controlleur consultation ainsi que les formulaires et vues Twig j'ai fais la commande :
    symfony console make:crud Consultation

Q10)
- Pour la création du modèle Traitement les commandes utilisées :
    symfony console make:entity Traitement
    symfony console make:migration
    symfony console doctrine:migration:migrate

Q11)
- Pour la création des vues et templates :
    symfony make:controller TraitementController
    symfony make:crud Traitement

Q12)
- Pour la création de l'Entité User
symfony composer require symfony/security-bundle
symfony console make:user
symfony console make:Entity User

utilisateurs:
-email : a@gmail , role : admin , mot de passe : 123
-email : m@gmail , role : medecin , mot de passe : 123
-email : p1@gmail , role : patient , mot de passe : 123
-email : p2@gmail , role : patient , mot de passe : 123
-email : p3@gmail , role : patient , mot de passe : 123
-email : p4@gmail , role : patient , mot de passe : 123
-email : p5@gmail , role : patient , mot de passe : 123

Q14)
- J'ai ajouter 2 ManytoOne a Consultation qui se supprime en cascade un pour le patient au quelle la consultation porte
    et l'autre sur le medecin.J'ai ajouter dans le show de traitement et consultation le nom du medecine et du patient.

Q17)
- J'ai fait des méthodes pour trouver toutes les consultations pour un User.

- Ajout du calcul du nombre de page par rapport au nombre de consultation.

- Ajout d'un controleur AllTraitementController pour afficher après tous les traitement du User : 
    symfony make:controller AllTraitementController

- Ajout du fait que si on accede a un traitement par la liste de tous nos traitement on reviens dessus apres avoir fait "Retour à la liste"

Q18)
- Ajout d'un controleur pour voir la liste des utilisateurs :
    symfony make:controller UserController

- Ajout de méthode de recherche de patient/medecin dans ConsultationRepository

- Ajout de recherche de tous les utilisateurs

Q19)
- Ajout filtre et barre de recherche

Q20)
- Ajout filtre et barre de recherche sur les utilisateurs

Q21)
- Ajout filtre sur les traitements (géneral et spécifique à une consultation) et tri sur les consultations

Q22)
-L’extension consistera à ajouter un système de paiement par carte bancaire pour régler les consultations en fonction de leur durée et du nombre de traitements.
Pour ce faire, il faudra ajouter un attribut temps de type integer et un autre booléen pour savoir si le paiement a été effectué pour une consultation.
Une fois connecté, le client verra son adresse email dans la barre de menu. En cliquant dessus, il pourra consulter les informations de son compte et ajouter une carte bancaire comme moyen de paiement.
Lorsqu’il consultera ses consultations, il pourra les payer si ce n’est pas déjà fait, en choisissant l’une de ses cartes bancaires ou en en ajoutant une nouvelle.
De plus, un filtre permettera de voir les consultations payée et à payer ou toutes.

Q23) 
-Ajout de sécurités sur les traitements et consultations au niveau des contrôleurs pour les actions edit, new, et delete.

-Consultation du profil connecté : en cliquant sur son email lorsqu’il est connecté, l’utilisateur peut également gérer ses cartes  bancaires.
Ajout d’un bouton de paiement pour les consultations si le patient n’a pas encore réglé sa consultation.
-Le bouton de paiement redirige vers une page où le montant s’affiche : temps * 2 + nombre de traitements * 25. L’utilisateur doit sélectionner l’une de ses cartes bancaires et cliquer pour payer. Si aucune carte n’est enregistrée, il faudra en ajouter une via le profil.
-Ajout d’un fond d’écran.
-Ajout de classes Bootstrap pour le style.



**Attention on a eu un probleme sur les migrations on a donc reconstruit complètement la migration donc si probleme quand on fait le :
    symfony console doctrine:fixture:load

étape a faire:
1- rm -f var/*.db
2- ls var/  (verifier qu'il n'y a pas de fichier en .db)
3- supprimer tous les migrations du fichier : migrations/
4- symfony console cache:clear
5- symfony console doctrine:migrations:sync-metadata-storage
6- symfony console make:migration
7- symfony console doctrine:migrations:migrate
8- symfony console doctrine:fixtures:load