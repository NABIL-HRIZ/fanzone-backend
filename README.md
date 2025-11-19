# FanZone - Back-end

Ce fichier README est destiné uniquement au dossier back_end.

## Description

Ce dépôt contient l'API et la logique serveur de FanZone, construite avec Laravel (PHP).

## Pré-requis

-   PHP 8+ installé
-   Composer
-   Une base de données (MySQL, PostgreSQL, SQLite...)

## Installation rapide

1. Cloner le dépôt et placer-vous dans `back_end`.
2. Installer les dépendances PHP :

    composer install

3. Copier le fichier d'environnement et générer la clé d'application :

    cp .env.example .env
    php artisan key:generate

4. Configurer les paramètres de la base de données dans `.env`.
5. Lancer les migrations et les seeders (si nécessaire) :

    php artisan migrate --seed

## Usage

-   Pour démarrer le serveur de développement local :

    php artisan serve

-   Points d'accès API principaux se trouvent dans `routes/api.php`.

## Tests

Le projet utilise PHPUnit / Pest. Pour lancer les tests :

./vendor/bin/phpunit

## Notes

-   Ce fichier est « le fichier juste pour back_end » — il contient les étapes minimales pour installer et démarrer l'API en local.
-   Pour des informations détaillées (architecture, événements, jobs, mails), voir les dossiers `app/` et `routes/`.

## Support

Si vous rencontrez des problèmes, ouvrez une issue ou contactez l'auteur du projet.
