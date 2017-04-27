# Installation

1. Copier le répertoire `twitter` dans le htdocs de votre installation XAMP
2. Créer la base de données infosec sur votre serveur de DB en lançant important, depuis `phpmyadmin`, le script `infosec.sql`. Ouvrir le script dans un éditeur de texte pour vérifer son contenu, et notamment pour savoir quelles données sont ajoutées dans la base de données par défaut, les noms des tables, etc.
3. Toujours depuis `phpmyadmin`, ajoutez un utilisateur (voir le fichier `public_html/.htaccess`) que *PHP* va utiliser pour se connecter à la base de données.
4. Ouvrez un navigateur et ouvrir l'url `//twitter/public_html`. Vous ne devriez pas voir d'erreur à l'écran.

# Twitter

Cette petite application permet d'envoyer de poster des messages. La page `index.php` affiche ces messages. Si vous êtes connectés (voir la base de données pour trouver des noms d'utilisateurs / MdP), vous pouvez en plus poster des messages et effacer vos propres messages (une faille vous permet également d'effacer les fichiers des autres utilisateurs).

Il y a également un user `admin` qui permet d'ajouter de nouveaux utilisateurs. Cette page ne devrait bien sûr par être accessible pour les autres utilisateurs (actuellement, elle l'est).

# fichiers

* `.htaccess` : contient les paramètres de connexion
* `admin.php` : la page qui ne devrait être accessible que par les admins
* `db.php` : des fonctions pour accéder à la base de données
* `index.php` : la page par défaut. S'affiche différemment pour les utilisateurs authentifiés et pour les invités
* `log.php` : permet de logguer des messages d'erreurs
* `user.php` : des fonctions pour la gestion de la session

# Notes

* Attention, lorsque vous testez par exemple XSS, utilisez de préférence Firefox, ou si vous insistez avec Chrome, ouvrez la console des outils de développement web.

* A la racine, deux scripts pourraient vous être utiles :
..# `init.php` : permet d'ajouter un utilisateur admin.
..# `insert_test_data.php` : permet d'ajouter des utilisateurs.
