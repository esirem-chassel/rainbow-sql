# rainbow-sql

## Qu'est-ce ?

Ce projet est un générateur / outil de recherche de rainbow table.

Il a été créé à des fins pédagogiques, dans le seul cadre d'usage pour de la réplication SQL.

## Installation et configuration

### Téléchargement

Clonez (ou télécharger la version complète de) l'outil dans votre répertoire web.

### Prérequis

Il est nécessaire, en plus de l'outil, d'avoir installé un serveur web (`nginx` qui est conseillé ou `apache` par exemple), et un serveur `mysql`/`mariadb` (`mariadb-server` ; [exemple sur Windows](https://mariadb.com/docs/server/server-management/install-and-upgrade-mariadb/installing-mariadb/binary-packages/installing-mariadb-msi-packages-on-windows)).

Une version de `PHP 8.1+` est nécessaire, et le plugin `pdo_mysql` doit être activé (ce qui est normalement le cas par défaut).

> [!Note]
> Depuis quelques versions, il est possible que `php-mysql` et `php-mbstring` ne soient plus installés par défaut.
> Si c'est votre cas, pensez à les installer manuellement.

### Configuration Web

Indiquez dans votre VHost (apache ou nginx) le répertoire `public`.

Un exemple de configuration nginx classique, à personnaliser évidemment selon votre cas :

```nginx
server {
       listen 80;
       listen [::]:80;

       server_name rainbow.local;

       root /var/www/rainbow-sql/public;
       index index.html index.php;

       location / {
               try_files $uri $uri/ =404;
       }

       location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        }
}
```

Si vous souhaitez "simplement" tester l'outil, vous pouvez également lancer le serveur built-in de PHP :
`cd public & php -S localhost:8000`

### Configuration minimale

Le fichier de configuration `.env` est à créer et remplir sur la base du fichier `.env.dist`.

L'instance SQL `writer` correspond au serveur SQL **principal**, tandis que l'instance SQL `reader` correspond à la réplique locale.

`dsn` (pour `Data Source Name`) correspond à la chaîne de connexion composée de : `<SGBDR>:host=<host>;dbname=<dbname>` avec respectivement `<SGBR>` le type de bases de données, `<host>` le nom d'hôte (facultatif) et `<dbname>` le nom de la base de données.

Un exemple pourrait être `mysql:host=localhost;dbname=testdb`.

Au premier lancement (web ou CLI), l'application va automatiquement se connecter à la base spécifiée avec les identifiants indiqués, et va créer d'elle-même les tables nécessaires.

### Crontab / Tâche programmée

L'instance peut générer des enregistrements dans la base, et doit le faire de façon périodique.

Configurez votre crontab pour exécuter régulièrement (conseillé : toutes les minutes) le fichier `bin/generate.php` (génération d'un hash unique) ou `bin/mass.php` (génération massive de hashs) selon votre objectif.

## Utilisation

L'interface web ne permet que de rechercher un hash, afin d'obtenir les collisions selon les algorithmes en place.

Il est possible de forcer la génération des hashs sur tous les algorithmes gérés en appellant manuellement `php -f bin/generate.php <clear>` en remplaçant `<clear>` par la chaîne désirée.


