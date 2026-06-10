### Initialisation du projet

Vérification des versions de docker, symfony, composer
![screenshot-vérification-des-versions](jour1/job-01-symfony/screenshot/verification-des-versions-composer-docker-symfony.png)

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
