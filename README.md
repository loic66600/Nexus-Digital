# 🎧 Nexus Digital - Site de Vente de Produits High-Tech

Bienvenue sur Nexus Digital, votre plateforme de vente en ligne pour les produits high-tech de dernière génération. Ce projet utilise Symfony et Docker pour offrir une expérience utilisateur fluide et performante.

## 🚀 Démarrage Rapide

### Prérequis

- Docker
- Docker Compose

1. Clonez le dépôt du projet sur votre machine locale.
2. Naviguez vers le dossier du projet et ajouer votre .env
3. Ouvrez un terminal et exécutez les commandes suivantes pour donner les permissions d'exécution aux scripts :
   ```bash
   chmod +x setup.sh
   chmod +x automate_setup.sh
   ```
4. Exécutez le script d'installation automatisée :
   ```bash
   ./automate_setup.sh
   ```
5. Après le lancement des conteneurs Docker, exécutez les commandes suivantes pour installer les dépendances :
   ```bash
   docker exec -it phpimmo composer install
   docker exec -it nodeimmo yarn
   docker exec -it nodeimmo yarn encore dev
   docker exec -it nodeimmo yarn encore dev --watch
   docker exec -it nodeimmo yarn add aos
   docker exec -it phpimmo composer require symfony/notifier
   docker exec -it phpimmo composer require fakerphp/faker --dev
   docker exec -it phpimmo composer require stripe/stripe-php
   ```
### Installation Initiale

## 🐳 Commandes Docker Courantes

- Lancer les conteneurs : `docker-compose up --build`
- Lancer les conteneurs en arrière-plan : `docker-compose up --build -d`
- Arrêter les conteneurs : `docker-compose down`
- Redémarrer les conteneurs : `docker-compose restart`

## 🛠 Commandes Symfony Utiles

- Créer un nouveau projet Symfony : `docker exec -it [nom_du_container_php] composer create-project symfony/skeleton ./`
- Ajouter un bundle : `docker exec -it [nom_du_container_php] composer req [nom_du_bundle]`
- Supprimer un bundle : `docker exec -it [nom_du_container_php] composer remove [nom_du_bundle]`
- Exécuter des commandes Symfony : `docker exec -it [nom_du_container_php] php bin/console`

## 🔧 Gestion des Droits

- Changer les droits d'accès : `sudo chown -R [nomUtilisateur ou uid]:[nom_du_groupe ou gid] app/`
- Accéder au shell du conteneur PHP : `docker exec -it [nom_du_container_php] sh`
- Modifier les permissions : 
  ```bash
  chown -R www-data:www-data ./
  chmod -R 755 ./
  ```

## 📦 Installation de Packages Supplémentaires

- Installer Twig : `docker exec -it [nom_du_container_php] composer req twig`
- Installer le Maker Bundle pour le développement : `docker exec -it [nom_du_container_php] composer req --dev symfony/maker-bundle`
- Installer le pack de sécurité : `docker exec -it [nom_du_container_php] composer req symfony/asset`

## 🤝 Contribution

Les contributions sont les bienvenues. N'hésitez pas à ouvrir une issue ou à soumettre une pull request.

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

---

Assurez-vous de remplacer `[nom_du_container_php]` par le nom réel de votre conteneur PHP dans Docker.
