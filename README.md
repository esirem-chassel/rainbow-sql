# rainbow-sql

## Qu'est-ce ?

Ce projet est un générateur / outil de recherche de rainbow table.

Il a été créé à des fins pédagogiques, dans le seul cadre d'usage pour de la réplication SQL.

## Installation et configuration

### Téléchargement

Clonez (ou télécharger la version complète de) l'outil dans votre répertoire web.

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

### Crontab / Tâche programmée

L'instance peut générer des enregistrements dans la base, et doit le faire de façon périodique.

Configurez votre crontab pour exécuter régulièrement (conseillé : toutes les minutes) le fichier `bin/generate.php`.

## Utilisation

L'interface web ne permet que de rechercher un hash, afin d'obtenir les collisions selon les algorithmes en place.

Il est possible de forcer la génération des hashs sur tous les algorithmes gérés en appellant manuellement `php -f bin/generate.php <clear>` en remplaçant `<clear>` par la chaîne désirée.
