Pour installer le backend réaliser les commandes avec un serveur de bases de données lancées (XAMPP dans mon cas) :

php bin/console doctrine:database:create
php bin/console d:m:m


La base de données est ainsi crée, maintenant il faut démarrer l'accès aux requêtes en local :

php -S localhost:8000 -t public/
