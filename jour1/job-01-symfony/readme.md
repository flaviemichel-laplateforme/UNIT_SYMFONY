# La Plateforme - Job 01 : Architecture & Base Symfony 7

![Symfony](https://img.shields.io/badge/Symfony-7.2-black?style=flat-square&logo=symfony)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql)

## 🛠️ Initialisation du projet

Vérification des versions de Docker, Symfony et Composer avant le lancement :
![screenshot-vérification-des-versions](screenshot/verification-des-versions-composer-docker-symfony.png)

### Architecture Docker (`docker-compose.yml`)

Notre environnement de développement repose sur une architecture multi-conteneurs moderne, permettant de séparer les responsabilités :

- **`app` (php:8.3-fpm)** : C'est le cœur de l'application. Ce conteneur exécute le code PHP de Symfony. Le code source est monté via un volume persistant, permettant de voir les modifications en temps réel.
- **`webserver` (nginx:stable)** : Le serveur web frontal. Il écoute sur le port 8080, distribue les fichiers statiques et transfère les requêtes dynamiques au conteneur `app` via le port 9000.
- **`database` (mysql:8.0)** : Le SGBD. Les données sont sauvegardées de manière permanente sur la machine hôte grâce au volume `db_data`.
- **`adminer` & `phpmyadmin`** : Interfaces graphiques de gestion de base de données, accessibles sur les ports 8081 et 8082.
- **`networks` (`symfony_network`)** : Un réseau privé virtuel isolant nos conteneurs et leur permettant de communiquer via résolution DNS interne.
- **`volumes`** : Points de montage garantissant la sauvegarde de nos données même lorsque les conteneurs sont détruits.

### Configuration du Serveur et de l'Image

**Configuration Nginx (`default.conf`)**
Ce fichier indique que le point d'entrée public est `/var/www/html/public`. Toutes les requêtes vers des fichiers inexistants sont redirigées vers `index.php` (Front Controller). Le bloc `location ~ \.php$` transmet l'exécution à PHP-FPM (port 9000). Les fichiers cachés sont bloqués par sécurité.

**Personnalisation de l'image PHP (`Dockerfile`)**
Construction d'une image sur-mesure basée sur `php:8.3-fpm`. Elle installe les utilitaires essentiels (`curl`, `unzip`, `git`), l'extension `pdo_mysql` pour la base de données, et télécharge globalement `Composer`.

---

## Déploiement de l'infrastructure

### Lancement des conteneurs

_Note de développement :_ J'ai rencontré une erreur lors du premier lancement, car Laragon était resté ouvert en fond. Le port MySQL était déjà utilisé. La fermeture de Laragon a résolu le conflit.

![Erreur de port MySQL](screenshot/erreur-port.png)
![Correction du conflit avec Laragon](screenshot/correction-bug-laragon-ouvert-mysql-port-bloque.png)
![Création du conteneur via Docker Compose](screenshot/docker-compose-up-d-creation-du-conteneur.png)
![Conteneur prêt](screenshot/container-ok.png)
![Lancement réussi](screenshot/lancement-reussi.png)

### Installation de Symfony

```bash
symfony new app --version="7.2.x" --webapp
```

`--version="7.2.x"` : verrouille l'installation sur la version mineure stable, assurant la compatibilité avec PHP 8.2+ et les attributs PHP natifs.

`--webapp` : installe l'architecture full stack (Doctrine, Twig, formulaires, sécurité) indispensable en production.

![Installation de Symfony](screenshot/etape-6-installer-symfony.png)

### Configuration & sécurité

1. Génération de la clé secrète

Pour sécuriser les jetons CSRF et les sessions, une clé cryptographique forte de 256 bits est générée via OpenSSL :

```bash
openssl rand -hex 32
```

![Configuration de la clé secrète](screenshot/configuration-.env-cle-secrete-.png)

2. Connexion à MySQL (.env)

Modification de la variable DATABASE_URL pour lier le framework au conteneur :

```env
mysql://symfony:symfony@symfony_db:3306/symfony
```

(Authentification symfony:symfony, sur l'hôte interne symfony_db).

3. Gestion des permissions

Dans le conteneur symfony_app, réalignement des permissions sur l'utilisateur système du serveur web (www-data) pour éviter les erreurs 500 :

```bash
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/var
```

![Entrée dans le conteneur](screenshot/entrer-conteneur.png)

### Développement : authentification et interface

Une fois l'infrastructure prête, le développement métier a été réalisé selon les standards Symfony 7 :

1. Base de données (Doctrine)

   Création de l'entité User (make:user).

   Génération et exécution des migrations SQL (make:migration et doctrine:migrations:migrate).

   Injection d'un utilisateur de test chiffré via Doctrine Fixtures (doctrine:fixtures:load).

2. Système de connexion (Security)

   Mise en place de l'authentification par formulaire (make:security:form-login).

   Configuration du pare-feu main dans security.yaml et activation de la protection CSRF.

3. Interface utilisateur (Twig & CSS)

   Création du HomeController avec routage par attribut PHP 8 (#[Route('/')]).

   Développement d'une interface Twig héritant de base.html.twig.

   Externalisation du design dans assets/styles/app.css (utilisation de Flexbox pour le header).

   Gestion conditionnelle de l'affichage : le menu propose le bouton "Connexion" aux visiteurs, et "Déconnexion" aux utilisateurs authentifiés (app.user).

![Connexion réussie](screenshot/connexion-reussi.png)
![Résultat final](screenshot/reussi.png)

Projet réalisé dans le cadre du Job 01 - Formation La Plateforme.
