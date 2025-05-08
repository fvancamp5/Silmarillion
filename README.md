# Rattrapage_web_mediatheque

Le projet vise la réalisation d'une application web permettant la gestion complète d'une médiathèque regroupant des livres, DVD et jeux vidéo. L'application devra disposer d'un Frontend clair et intuitif, un Backend structuré en API REST, et une base de données relationnelle (BDD).

## Installation

**Partie Front-end**
- Installation de Composer et de Twig comme moteur de template.
- Installation d'extensions Snippet HTML, CSS, JS pour la visibilité.
- Installation du live editor pour avoir un aperçu des modifications en direct.


## Organisation

**Style**
- Création d'une maquette Figma :  
  [Lien vers la maquette](https://www.figma.com/proto/i77g0HxKCzoeSqL1fbiiYo/Web-Rattrapage?page-id=0%3A1&node-id=8-51&p=f&viewport=-36%2C201%2C0.1&t=gTSKsJ55MhXWF2Oe-1&scaling=scale-down&content-scaling=fixed&starting-point-node-id=8%3A51)
- Définition d'une charte graphique.
- Utilisation de Bulma.

**Code**

- Le code est architecturé selon le modèle Model-View-Controller, mais sous Symfony, ce qui implique des changements de nom :
    - Les Vues sont dans le dossier `templates`.
    - Les Controllers sont dans `src/Controller` (avec les routes), `src/Repository` (avec des fonctions permettant la manipulation des données) et `src/Entity` (les classes possèdent des méthodes `get` et `set` pour récupérer et modifier les données directement intégrées).
    - Les Models sont définis dans `src/Entity` (Doctrine définit des variables dans les classes qui correspondent aux champs des tables).

(Entity fait le lien avec la BDD, définit ou garde les champs des tables et propose des méthodes pour récupérer les objets associés grâce à Doctrine. Tandis que Repository permet de créer des méthodes personnalisées pour réaliser des requêtes, non pas avec PDO, mais avec Connection.)

- Pour la création de la Base De Données, un dictionnaire, un MCD, un MLD et un MPD ont été réalisés pour définir les besoins et le format de la BDD.
   - Dictionnaire de données :  
     ![Dictionnaire de données](imgREADME/image-7.png)
   - [Modèles de Données (en partant du principe qu'un exemplaire d'un média est unique et que si plusieurs personnes empruntent le même média, ce seront deux médias différents)](imgREADME/Data_Models_mediatheque.pdf)

   - Nous avons donc une BDD avec 4 tables : 
        - `user` contenant les informations des utilisateurs du site.
        - `medias` contenant les informations des médias.
        - `loan` contenant les informations des emprunts réalisés.
        - `history`, qui est une copie de `loan` où l'on ne peut retirer des éléments.

   - Création des tables avec l'ORM Doctrine de Symfony, puis migration sur le serveur MySQL (voir dans `migrations/Version20250427162424.php`).

En partant du principe qu'un administrateur est un utilisateur avec des privilèges, un administrateur se connecte avec son compte comme un utilisateur normal, mais aura des accès supplémentaires. Le statut admin est défini par le booléen `status` de la table `user`.

## Utilisation

**La base de données MySQL a été vidée**. Il faut être connecté pour voir son profil, consulter ses emprunts ou réaliser un nouvel emprunt. L'ajout, la modification et la suppression des médias nécessitent un compte administrateur. Grâce à l'authentification sécurisée d'API Platform, le système génère un token JWT lors de la connexion. Par défaut, un seul compte administrateur existe avec les identifiants suivants :

- Email : `admin@cesi.fr`
- Mot de Passe : `4dm1n_p422w0rd`

**Médias**
Les médias déjà empruntés ne sont pas visibles depuis la page d'accueil, mais ils apparaissent dans les résultats de recherche ou peuvent être consultés directement via l'URL `Silmarillion/medias/{id}` si vous connaissez leur identifiant.
Avec un compte avec le role admin, vous pouvez : 
- Ajouter un média depuis la page d'accueil en remplissant les champ (en ce qui concerne l'image, il faut la télécharger et la placer dans public/images)
- Modifier un média 
- Supprimer un média (!Attention : l'action est définitive!)

**Recherche**
La recherche s'effectue depuis la barre de navigation :
- Vous pouvez laisser le champ vide pour afficher tous les médias
- Vous pouvez saisir des termes pour effectuer une recherche avancée
- Le format de pagination peut être ajusté via le sélecteur disponible sur la page de recherche

**Accueil**
En s'identifiant, deux boutons apparaissent sur la page d'accueil : 
- Un pour voir les informations de son compte ainsi qu'un graphique permettant de voir les statistiques des emprunt
- Un pour voir les emprunts actuels ainsi que l'historique de tous les emprunts


**En cas de problème**
Si la moindre fonctionnalité ne fonctionne pas, une vidéo démonstration est disponible (vidéo réalisée à partir de la version v1.0.2 du repo Mediatheque_Silmarillion) <video controls src="silmarillion_démo.mp4" title="Title"></video>