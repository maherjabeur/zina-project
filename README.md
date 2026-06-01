# Zina Project / Bella Couture

Application e-commerce Symfony pour une boutique de mode feminine. Le projet permet de presenter un catalogue de produits, gerer un panier, passer des commandes et administrer les contenus de la boutique depuis un espace back-office.

## Presentation

Bella Couture propose une experience de boutique en ligne avec :

- page d'accueil avec slider et produits en vedette ;
- catalogue produits avec categories, tailles, couleurs, stock et promotions ;
- fiche detail produit avec ajout au panier ;
- panier, validation de commande et suivi des statuts ;
- formulaire de contact ;
- espace d'administration protege ;
- gestion des produits, images, categories, tailles, promotions, commandes, slider et frais de livraison ;
- notifications email pour les commandes et messages de contact.

## Technologies

- PHP 8.1 ou superieur
- Symfony 6.4
- Doctrine ORM
- Twig
- Bootstrap / Font Awesome
- KnpPaginatorBundle
- VichUploaderBundle
- PHPUnit

## Prerequis

Installer sur la machine :

- PHP 8.1+
- Composer
- MySQL ou MariaDB
- Symfony CLI, optionnel mais recommande
- Docker, optionnel si vous souhaitez lancer une base via `compose.yaml`

Verifiez les versions :

```bash
php -v
composer -V
symfony -v
```

## Installation locale

1. Cloner le projet puis entrer dans le dossier :

```bash
git clone <url-du-repository>
cd zina-project
```

2. Installer les dependances PHP :

```bash
composer install
```

3. Creer le fichier d'environnement local :

```bash
cp .env .env.local
```

Sur Windows PowerShell :

```powershell
Copy-Item .env .env.local
```

4. Configurer la base de donnees dans `.env.local`.

Configuration MySQL par defaut du projet :

```dotenv
DATABASE_URL="mysql://root:@127.0.0.1:3306/zina-project?charset=utf8mb4"
```

Adaptez l'utilisateur, le mot de passe, le port et le nom de base selon votre environnement.

5. Creer la base de donnees :

```bash
php bin/console doctrine:database:create
```

6. Creer le schema.

Le dossier `migrations/` ne contient pas encore de migrations versionnees. Pour une installation locale rapide :

```bash
php bin/console doctrine:schema:update --force
```

Alternative recommandee pour un projet maintenu :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

7. Charger des donnees de test :

```bash
php bin/console doctrine:fixtures:load
```

Confirmez la suppression/recreation des donnees quand la console le demande.

8. Lancer le serveur :

Avec Symfony CLI :

```bash
symfony server:start
```

Sans Symfony CLI :

```bash
php -S 127.0.0.1:8000 -t public
```

L'application sera disponible sur :

```text
http://127.0.0.1:8000
```

## Option Docker pour la base de donnees

Le fichier `compose.yaml` fournit un service PostgreSQL, alors que le `.env` du projet pointe actuellement vers MySQL. Si vous souhaitez utiliser Docker avec PostgreSQL :

```bash
docker compose up -d
```

Puis adaptez `.env.local` :

```dotenv
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:<port-expose>/app?serverVersion=16&charset=utf8"
```

Pour connaitre le port expose par Docker :

```bash
docker compose ps
```

## Comptes de test

Avec `doctrine:fixtures:load`, un compte administrateur est cree :

```text
Email : admin@boutique-femme.com
Mot de passe : admin123
```

Des utilisateurs de test sont aussi ajoutes avec le mot de passe :

```text
user123
```

Une commande personnalisee existe egalement :

```bash
php bin/console app:load-fixtures
```

Elle cree notamment :

```text
Admin : admin@boutique.com / admin123
User  : user@boutique.com / user123
```

## Pages principales

- `/` : accueil
- `/products` : catalogue produits
- `/product/{id}` : detail produit
- `/cart` : panier
- `/checkout` : validation de commande
- `/contact` : contact
- `/login` : connexion
- `/admin` : tableau de bord administrateur

## Commandes utiles

Vider le cache :

```bash
php bin/console cache:clear
```

Lister les routes :

```bash
php bin/console debug:router
```

Verifier la configuration :

```bash
php bin/console about
```

Lancer les tests :

```bash
php bin/phpunit
```

## Emails

Par defaut, les emails sont desactives :

```dotenv
MAILER_DSN=null://null
```

Pour tester les emails en local, configurez un transport SMTP ou lancez le service Mailpit defini dans `compose.override.yaml`, puis adaptez `MAILER_DSN` dans `.env.local`.

Exemple Mailpit :

```dotenv
MAILER_DSN=smtp://127.0.0.1:<port-smtp>
```

## Structure du projet

```text
src/Controller/     Controleurs front-office et back-office
src/Entity/         Entites Doctrine
src/Form/           Formulaires Symfony
src/Repository/     Requetes Doctrine personnalisees
src/Service/        Services metier, uploads et notifications
templates/          Vues Twig
public/             Point d'entree, assets publics et uploads
assets/             Assets geres par Symfony AssetMapper
config/             Configuration Symfony
tests/              Tests automatises
```

## Notes de production

Avant une mise en production :

- generer un `APP_SECRET` unique et ne pas le versionner ;
- configurer une vraie base de donnees de production ;
- configurer `MAILER_DSN` ;
- desactiver le mode debug avec `APP_ENV=prod` ;
- executer les migrations au lieu de `doctrine:schema:update --force` ;
- proteger les dossiers d'upload et verifier les permissions serveur ;
- changer tous les comptes et mots de passe de test.

