🧘‍♀️ Loryane Essentielle — E-commerce Bien-Être

📌 Présentation du projet

Loryane Essentielle est une application web e-commerce dédiée au bien-être.
Elle permet de consulter un catalogue de produits (huiles essentielles, packs bien-être, etc.), gérer un panier, passer des commandes et effectuer un paiement sécurisé.

Ce projet est réalisé dans le cadre de la formation Développeur Web & Web Mobile (ENI), avec un objectif double :
•	Valider les compétences techniques attendues
•	Construire une base solide pour un projet réel exploitable

⸻

⚙️ Stack technique
•	Backend : Symfony
•	Base de données : MySQL
•	ORM : Doctrine
•	Frontend : Twig
•	Gestion des dépendances : Composer
•	Paiement (prévu) : Stripe

⸻

🚀 Installation du projet

1. Cloner le projet
```bash
git clone <url-du-repo>
cd loryane-essentielle
```
2. Installer les dépendances
```bash
composer install
```
3. Configurer l’environnement
   Créer ou modifier le fichier .env :
```env
DATABASE_URL="mysql://root:root@127.0.0.1:8889/loryane"
```
⸻

🗄️ Base de données

Création de la base
```bash
php bin/console doctrine:database:create
```
Exécuter les migrations
```bash
php bin/console doctrine:migrations:migrate
```
Charger les données (fixtures)
```bash
php bin/console doctrine:fixtures:load
```
⸻

▶️ Lancement du projet

```bash
symfony server:start
```
Accès : http://localhost:8000

⸻

📁 Structure du projet

src/
├── Controller/       → Gestion des routes et logique applicative
├── Entity/           → Modèle de données (Doctrine)
├── Repository/       → Accès aux données
├── DataFixtures/     → Données de test

templates/
├── produit/          → Pages catalogue produit
├── base.html.twig    → Layout principal

migrations/            → Versions de la base de données


⸻

🧭 Roadmap du projet

🔹 Itération 0 — Fondations
•	Préparation du projet
•	Modélisation UML (cas d’utilisation, classes, séquences, états)
•	Initialisation Symfony et base de données
•	Création entités, migrations et fixtures

⸻

🔹 Itération 1 — Catalogue produit
•	Gestion des produits (CRUD)
•	Affichage catalogue (liste + détail)
•	Gestion des catégories
•	Structuration du catalogue

⸻

🔹 Itération 2 — Utilisateur
•	Gestion utilisateur
•	Authentification et rôles
•	Gestion des adresses

⸻

🔹 Itération 3 — Panier
•	Service panier
•	Gestion des produits dans le panier
•	Calcul du total

⸻

🔹 Itération 4 — Commande
•	Création commande depuis panier
•	Gestion des lignes de commande
•	Consultation des commandes

⸻

🔹 Itération 5 — Paiement
•	Intégration Stripe
•	Webhook
•	Gestion du statut des commandes

⸻

🔹 Itération 6 — Administration
•	Back-office admin
•	Gestion produits et commandes
•	Gestion du stock

⸻

🔹 Itération 7 — Finalisation
•	Sécurité
•	Optimisation
•	Tests
•	UX/UI

⸻

🔹 Itération 8 — Mise en production
•	Déploiement serveur
•	Configuration HTTPS
•	Tests en production
•	Maintenance
⸻

🎯 Objectifs du projet
•	Construire une application e-commerce complète
•	Respecter les bonnes pratiques Symfony
•	Garantir la cohérence des données métier
•	Préparer une mise en production réelle

⸻

📌 État actuel
✔ Backend opérationnel
✔ Base de données stable
✔ Catalogue produit fonctionnel (liste)

⸻

📎 Auteur

Projet réalisé par Séverine Boré dans le cadre d’une reconversion en développement web.
