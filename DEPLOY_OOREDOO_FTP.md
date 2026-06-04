# Deploiement Ooredoo Host Advanced par FTP

Ce projet est une application Symfony. Sur un hebergement mutualise avec FTP/cPanel, le plus simple est de preparer le projet en local, puis d'uploader les fichiers prepares.

## 1. Preparer en local

```bash
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug
```

Si `var/cache/prod` a ete cree en local, vous pouvez l'uploader aussi. Si le serveur refuse a cause des permissions, videz `var/cache/prod` sur le FTP et laissez Symfony le recreer.

## 2. Configurer la base Ooredoo

Dans le panel Ooredoo/cPanel, creez une base MySQL, un utilisateur et un mot de passe.

Mettez ensuite la vraie connexion dans `.env` ou `.env.local` :

```dotenv
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL="mysql://UTILISATEUR:MOT_DE_PASSE@localhost:3306/NOM_BASE?charset=utf8mb4"
```

Important : ne gardez pas une ancienne connexion locale dans `.env.local.php`. Si ce fichier existe et pointe vers `127.0.0.1/zina-project`, supprimez-le avant upload ou regenerez-le avec les bonnes valeurs.

## 3. Upload FTP

Uploadez tout le projet, y compris :

- `public/`
- `src/`
- `templates/`
- `config/`
- `vendor/`
- `var/`
- `.env`
- `.htaccess`

Idealement, le domaine doit pointer vers le dossier `public`. Si ce n'est pas possible, le `.htaccess` ajoute a la racine redirige les requetes vers `public` et bloque les dossiers sensibles.

## 4. Base de donnees

Si vous avez un acces SSH :

```bash
php bin/console doctrine:migrations:migrate --env=prod --no-debug
```

Sans SSH, utilisez phpMyAdmin pour importer votre base locale. Ensuite, appliquez les migrations/index si elles ne sont pas deja presentes.

## 5. Verification rapide

- `/` doit afficher la boutique.
- `/products` doit afficher le catalogue.
- `/cart` doit fonctionner avec les sessions.
- `/admin` doit afficher la page admin apres connexion.
- Verifiez que `https://votre-domaine/.env` ne s'ouvre pas.
