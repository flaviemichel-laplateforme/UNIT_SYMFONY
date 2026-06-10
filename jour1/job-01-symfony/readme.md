### Initialisation du projet

Vérification des versions de docker, symfony, composer
![screenshot-vérification-des-versions](/jour1/job-01-symfony/screenshot/verification-des-versions-composer-docker-symfony.png)

### Architecture Docker (docker-compose.yml)

Notre environnement de développement repose sur une architecture multi-conteneurs moderne, permettant de séparer les responsabilités :

- **`app` (php:8.3-fpm)** : C'est le cœur de l'application. Ce conteneur exécute le code PHP de Symfony. Le code source est monté via un volume persistant, ce qui permet de voir les modifications en temps réel sans redémarrer le conteneur.
- **`webserver` (nginx:stable)** : Le serveur web frontal. Il écoute sur le port 8080, distribue les fichiers statiques (assets) et transfère les requêtes dynamiques au conteneur `app` via le port 9000.
- **`database` (mysql:8.0)** : Le système de gestion de base de données. Les données sont sauvegardées de manière permanente sur la machine hôte grâce au volume `db_data`.
- **`adminer` & `phpmyadmin`** : Deux interfaces graphiques de gestion de base de données, liées directement au conteneur `database` et accessibles respectivement sur les ports 8081 et 8082.
- **`networks` (`symfony_network`)** : Un réseau privé virtuel isolant nos conteneurs du reste du système, tout en leur permettant de communiquer entre eux via leurs noms (résolution DNS interne).
- **`volumes`** : Les points de montage qui garantissent la sauvegarde de nos données (bases de données, cache Symfony, et logs PHP/Nginx) même lorsque les conteneurs sont détruits.

### Étape 4 : Préparer le fichier default.conf

Configuration Nginx (default.conf) : Ce fichier définit comment le serveur web traite les requêtes HTTP entrantes sur le port 80. Il indique que le point d'entrée public de l'application est le dossier /var/www/html/public (standard Symfony). Toutes les requêtes vers des fichiers qui n'existent pas physiquement sont redirigées vers le fichier index.php (le Front Controller de Symfony) via le bloc location /. Le bloc location ~ \.php$ transmet ensuite l'exécution de ces scripts PHP au conteneur app (PHP-FPM) sur le port 9000. Enfin, l'accès aux fichiers cachés comme .htaccess est bloqué par mesure de sécurité.

### Étape 5 : Préparer le fichier Dockerfile

Personnalisation de l'image PHP (Dockerfile) : Ce fichier permet de construire une image Docker sur-mesure basée sur php:8.3-fpm.
Il met à jour le gestionnaire de paquets (apt-get) pour installer des utilitaires essentiels au développement (curl, unzip, git).
Ensuite, il télécharge et installe globalement Composer (le gestionnaire de dépendances de PHP), qui est strictement requis pour créer et gérer un projet Symfony.

### Lancement des conteneurs

J'ai rencontré une erreur lors du lancement, cmme Laragon était resté ouvert en fond , le port mysql était déja utilisé ce qui me crée une erreur lors du lancement.

![erreur-lancement](/jour1/job-01-symfony/screenshot/erreur-port.png)
![lancement après correction , arrêt de laragon](/jour1/job-01-symfony/screenshot/correction-bug-laragon-ouvert-mysql-port-bloque.png)

### Étape 6 : Installer Symfony

- symfony new app : Initialise un nouveau projet en téléchargeant l'architecture native du framework directement dans le répertoire app. Ce dossier est automatiquement synchronisé avec le conteneur Docker grâce au volume configuré dans notre docker-compose.yml.

- --version="7.2.x" : Verrouille l'installation sur la version mineure stable de Symfony 7. Cela assure une parfaite compatibilité avec PHP 8.2+, l'exploitation des fonctionnalités modernes (comme les attributs PHP natifs) et la pérennité du code face aux futures mises à jour.

- --webapp : Ce flag télécharge la configuration complète pour une application web traditionnelle (Full-Stack). Plutôt que de partir d'un squelette vide (microframework), cette option installe immédiatement les composants indispensables en production : l'ORM (Doctrine), le moteur de rendu (Twig), le gestionnaire de formulaires, le validateur de données, et le framework de sécurité.

Aperçu de l'architecture professionnelle générée :L'exécution de cette commande génère une arborescence standardisée, facilitant le travail collaboratif et la maintenance :

- config/ : Centralise la configuration des routes, des services injectés et des bundles tiers.
- public/ : L'unique point d'entrée exposé sur le web (lié à notre fichier default.conf de Nginx). Il contient le fichier index.php (Front Controller) qui intercepte toutes les requêtes HTTP.
- src/ : Le cœur architectural où sera écrit l'ensemble du code métier (Contrôleurs, Entités Doctrine, Services, Repositories).
- templates/ : Regroupe l'ensemble des fichiers de l'interface utilisateur gérés par le moteur de template Twig.
- .env : Fichier clé stockant les variables d'environnement spécifiques à l'infrastructure locale (identifiants de base de données, clés secrètes).

commande: symfony new app --version="7.2.x" --webapp
![lancement de l'installation de symfony](/jour1/job-01-symfony/screenshot/etape-6-installer-symfony.png)

Lancement réussi interface docker desktop
![Lancement réussi interface docker desktop](/jour1/job-01-symfony/screenshot/lancement-reussi.png)

Lancement réussi , aperçu de ma structure et du dossier app/
![structure](/jour1/job-01-symfony/screenshot/reussi.png)

### Étape 5 page 7 : Configurer la base de données sur le projet

Configuration des mes variables d'environnements dans le .env et création d'une clé secrète sécurisé

![configuration .env et sécurité](/jour1/job-01-symfony/screenshot/configuration-.env-cle-secrete-.png)

##
