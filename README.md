# 📊 phpPlanFlow - Système de Gestion de Projet

phpPlanFlow est une application web de gestion de projet développée en **PHP natif** pour le backend, avec **Tailwind CSS** pour le style et **JavaScript natif** pour l'interactivité côté frontend. Conçu pour aider les équipes à organiser, suivre et collaborer efficacement sur des projets, phpPlanFlow offre un ensemble complet de fonctionnalités pour la gestion des utilisateurs, des tâches et des projets.

---

## 🚀 Fonctionnalités

- **Gestion des utilisateurs** :

  - Création de comptes utilisateurs.
  - Gestion des rôles et des permissions (Administrateur, Responsable de projet, Membre de l'équipe).

- **Gestion des projets** :

  - Ajouter, modifier et supprimer des projets.
  - Suivi des dates de début et de fin, du statut du projet (En cours, Terminé, Annulé) et du responsable du projet.

- **Gestion des tâches** :

  - Ajouter, modifier et supprimer des tâches pour chaque projet.
  - Suivi des statuts (À faire, En cours, Terminé, Bloquée) et des priorités (Haute, Moyenne, Basse).
  - Affectation des tâches aux utilisateurs.
  - Ajout de commentaires pour faciliter la collaboration.

- **Rapports et tableaux de bord** :

  - Génération de rapports sur l'avancement des projets.
  - Visualisation de la charge de travail des utilisateurs.

- **Recherche et navigation** :

  - Recherche avancée par mots-clés pour les projets et les tâches.

- **Notifications** :

  - Notifications en temps réel pour les changements de statut et autres événements importants.

- **Système de permissions** :
  - Contrôle d'accès basé sur les rôles pour protéger les fonctionnalités critiques.

---

## 🗃️ Modèle de données

- **Projet** : ID unique, nom, description, date de début, date de fin prévue, statut, responsable du projet.
- **Tâche** : ID unique, nom, description, dates (début, fin prévue, fin réelle), statut, priorité, affectataire.
- **Utilisateur** : ID unique, nom, email, mot de passe (haché), rôle.
- **Commentaire** : Texte, date, auteur (utilisateur lié à une tâche).

---

## 🛠️ Technologies utilisées

- **Backend** : PHP natif
- **Frontend** : Tailwind CSS, JavaScript natif
- **Base de données** : MySQL

---

## 📂 Installation et configuration

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/ton-utilisateur/ton-depot.git
   ```
2. Importez la base de données (`database.sql`) dans MySQL.
3. Configurez le fichier de connexion à la base de données (`config/db.php`).
4. Lancez l'application sur un serveur local (XAMPP, WAMP, ou autre).

---

## 📸 Captures d'écran

_À venir : Ajoutez des captures d'écran de l'interface utilisateur pour donner un aperçu visuel de l'application._

---

## 📧 Contact

Pour toute question ou suggestion, veuillez contacter **Yassine Kamouss**.
