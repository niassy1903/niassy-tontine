# Niassy Tontine

Plateforme Laravel de gestion des tontines, des membres, des cotisations et de la trésorerie.

## Stack

- Laravel 13 / PHP 8.4+
- Blade, Tailwind CSS 4 via Vite et JavaScript vanilla
- Bootstrap Icons et compatibilité des vues historiques conservées pendant la migration
- Eloquent avec SQLite en développement local
- MySQL recommandé en production
- Sessions Laravel, CSRF, Form Requests, rate limiting, middleware et transactions DB

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Avant le seeding, définir `NIASSY_ADMIN_PASSWORD` dans l’environnement pour choisir le mot de passe du compte `admin@niassytontine.com`. Aucun secret ne doit être commité.

## MySQL

Renseigner `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` et `DB_PASSWORD` dans l’environnement avant de lancer les migrations.

Pour un fournisseur qui exige TLS, comme Aiven, télécharger son certificat CA et définir `MYSQL_ATTR_SSL_CA` vers le chemin lisible par l'application. Sur Render, un fichier secret nommé `ca.pem` est accessible sous `/etc/secrets/ca.pem`. La vérification du certificat serveur reste activée lorsque ce paramètre est défini.

## Fonctionnalités disponibles

- Accueil public bleu/blanc et découverte des tontines publiques
- Inscription, connexion, remember me, régénération de session et limitation des tentatives
- Création de tontines avec visibilité, fréquence et montant de cotisation
- Dashboard utilisateur avec statistiques issues de la base
- Interface responsive modernisée avec thème Tailwind bleu/blanc
- Gestion des membres et isolation par appartenance à la tontine
- Cotisations, paiements partiels et validation par un autre membre habilité
- Trésorerie et dépenses avec solde recettes moins dépenses
- Backoffice Super Admin séparé pour les utilisateurs
- Commande planifiée `php artisan tontine:check-overdue`
- Invitations par email avec acceptation contrôlée par adresse
- Notifications de paiement et centre d’alertes
- Rapports financiers et export CSV

## Sécurité

Les formulaires utilisent CSRF et validation Laravel. Les paiements critiques passent par `DB::transaction()`. Les routes d’administration passent par `EnsureSuperAdmin`; les paiements ne peuvent pas être validés par leur auteur.

## Déploiement

### Render avec Docker

Créer un Web Service Render à partir du dépôt et choisir **Docker** comme environnement. Le `Dockerfile` compile les assets Vite et configure Apache pour écouter sur le port `PORT` fourni par Render.

Dans les variables d'environnement du service, définir au minimum `APP_KEY` (générée avec `php artisan key:generate --show`), `APP_URL` et les paramètres `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` et `DB_PASSWORD` pour la base de données de production. Utiliser une base externe persistante : le système de fichiers du conteneur Render n'est pas persistant. Ne pas utiliser la base SQLite locale en production sans disque persistant.

Après le premier déploiement et la configuration de la base, exécuter `php artisan migrate --force` depuis le shell du service. Pour les fichiers téléversés, configurer un stockage persistant ou un service de stockage distant, puis exécuter `php artisan storage:link`. Les workers de queue et le scheduler Laravel doivent être déployés comme processus séparés si l'application en a besoin.