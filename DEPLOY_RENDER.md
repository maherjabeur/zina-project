# Deploiement Render

Cette configuration deploie l'application Symfony avec Docker, Apache/PHP 8.3 et PostgreSQL.

## Fichiers ajoutes

- `render.yaml` cree un Web Service Docker et une base Render Postgres.
- `Dockerfile` installe PHP, Composer et l'extension `pdo_pgsql`.
- `docker/render-entrypoint.sh` vide le cache Symfony puis lance `doctrine:migrations:migrate` au demarrage.
- `migrations/Version20260601000000.php` cree le schema PostgreSQL et importe les donnees de `zina-project.sql`.

## Deploiement

1. Pousser le projet sur GitHub/GitLab/Bitbucket.
2. Dans Render, choisir **New > Blueprint**.
3. Connecter le repo qui contient `render.yaml`.
4. Renseigner les variables marquees `sync: false` si l'envoi email doit etre actif.
5. Lancer le blueprint.

Render injecte automatiquement `DATABASE_URL` depuis la base `zina-project-db`, dont le nom PostgreSQL est `boutique`.

## Notes

- La base gratuite Render Postgres expire apres 30 jours. Pour un vrai site en production, choisir un plan payant.
- Les migrations se lancent au demarrage du conteneur. Le dump MySQL n'est pas importe directement; il est parse par la migration initiale et insere dans PostgreSQL.
- `PHPMAILER_DRY_RUN=true` est garde par defaut pour eviter l'envoi reel d'emails tant que SMTP n'est pas configure.
