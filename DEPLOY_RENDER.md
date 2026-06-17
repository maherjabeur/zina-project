# Deploiement Render

Cette configuration deploie l'application Symfony avec Docker, Apache/PHP 8.3 et MySQL.

## Fichiers ajoutes

- `render.yaml` cree un Web Service Docker. La variable `DATABASE_URL` doit pointer vers votre base MySQL.
- `Dockerfile` installe PHP, Composer et les extensions `pdo_mysql` et `pdo_pgsql`.
- `docker/render-entrypoint.sh` vide le cache Symfony puis lance `doctrine:migrations:migrate` au demarrage.
- `migrations/Version20260601000000.php` cree le schema et importe les donnees de `zina-project.sql`.

## Deploiement

1. Pousser le projet sur GitHub/GitLab/Bitbucket.
2. Dans Render, choisir **New > Blueprint**.
3. Connecter le repo qui contient `render.yaml`.
4. Renseigner `DATABASE_URL` avec l'URL MySQL.
5. Renseigner les variables email marquees `sync: false` si l'envoi email doit etre actif.
6. Lancer le blueprint.

Exemple `DATABASE_URL`:

```env
mysql://USER:PASSWORD@HOST:3306/sql7830570?serverVersion=8.0.32&charset=utf8mb4
```

## Notes

- Render propose une documentation pour deployer MySQL via Docker + disque persistant, ou vous pouvez utiliser une base MySQL externe.
- Les migrations se lancent au demarrage du conteneur. Le dump MySQL est importe par la migration initiale si la base est vide.
- `PHPMAILER_DRY_RUN=true` est garde par defaut pour eviter l'envoi reel d'emails tant que SMTP n'est pas configure.
