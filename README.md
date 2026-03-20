# rest_mediatekdocuments

Ce dépôt est un fork du dépôt d'origine [CNED-SLAM/rest_mediatekdocuments](https://github.com/CNED-SLAM/rest_mediatekdocuments) qui contient, dans son readme, la présentation de la structure de base de l'API et comment l'exploiter.

## Présentation

Cette API REST, écrite en PHP, permet d'exécuter des requêtes SQL sur la base de données `mediatek86` (MySQL). Elle est protégée par une authentification Basic (login : `admin`, pwd : `adminpwd`).

Elle répond aux demandes de l'application C# MediaTekDocuments :
[github.com/EliasKomur/mediatekdocuments](https://github.com/EliasKomur/mediatekdocuments)

L'API est déployée en ligne à l'adresse :
```
https://eliask.alwaysdata.net
```

---

## Fonctionnalités ajoutées

Par rapport au dépôt d'origine, les fonctions suivantes ont été ajoutées dans `MyAccessBDD.php` :

- **selectAllLivres** – récupère la liste des livres avec jointures (genre, public, rayon)
- **selectAllDvd** – idem pour les DVD
- **selectAllRevues** – idem pour les revues
- **selectExemplairesRevue** – récupère les exemplaires d'une revue par son id
- **selectExemplairesLivreDvd** – récupère les exemplaires d'un livre ou DVD
- **selectTableSimple** – récupère les lignes des tables simples (genre, public, rayon, etat, suivi) triées par libellé
- **selectUtilisateur** – vérifie les identifiants de connexion et retourne l'utilisateur correspondant
- **selectCommandesLivreDvd** – récupère les commandes d'un livre ou DVD
- **selectCommandesRevue** – récupère les abonnements d'une revue
- Gestion complète CRUD sur les tables : `document`, `livre`, `dvd`, `revue`, `livres_dvd`, `exemplaire`, `commande`, `commandedocument`, `abonnement`

---

## Ressources

- **Script SQL de la BDD** : `mediatek86.sql` à la racine du projet
- **Documentation technique** : disponible dans le README du dépôt d'origine

---

## Installation en local

### Prérequis
- WampServer (ou équivalent)
- Composer
- Postman (pour les tests)

### Étapes

**1. Cloner le dépôt dans le dossier `www` de WampServer**
```bash
git clone https://github.com/EliasKomur/rest_mediatekdocuments.git
```
Renommer le dossier en `rest_mediatekdocuments` si nécessaire.

**2. Installer les dépendances**
```bash
cd rest_mediatekdocuments
composer install
```

**3. Créer la base de données**

Dans phpMyAdmin, créer la base `mediatek86` puis importer le fichier `mediatek86.sql` depuis la racine du projet.

**4. Configurer le fichier `.env`**

À la racine du projet, créer ou modifier le fichier `.env` :
```env
BDD_LOGIN=root
BDD_PWD=
BDD_BD=mediatek86
BDD_SERVER=localhost
BDD_PORT=3306
API_LOGIN=admin
API_PWD=adminpwd
```

**5. Tester avec Postman**

Configurer l'authentification dans Postman :
- Onglet **Authorization** → Type **Basic Auth**
- Username : `admin`
- Password : `adminpwd`

---

## Exploitation de l'API

Adresse en local : `http://localhost/rest_mediatekdocuments/`<br>
Adresse en ligne : `https://eliask.alwaysdata.net/`

### Récupérer (GET)
```
GET https://eliask.alwaysdata.net/table
GET https://eliask.alwaysdata.net/table/{champs_json}
```

### Insérer (POST)
```
POST https://eliask.alwaysdata.net/table
Body (x-www-form-urlencoded) : champs={"champ1":"valeur1","champ2":"valeur2"}
```

### Modifier (PUT)
```
PUT https://eliask.alwaysdata.net/table/{id}
Body (x-www-form-urlencoded) : champs={"champ1":"valeur1"}
```

### Supprimer (DELETE)
```
DELETE https://eliask.alwaysdata.net/table/{champs_json}
```

### Exemples de routes disponibles
| Route | Méthode | Description |
|-------|---------|-------------|
| `/document` | GET | Liste tous les documents |
| `/livre` | GET | Liste tous les livres |
| `/dvd` | GET | Liste tous les DVD |
| `/revue` | GET | Liste toutes les revues |
| `/genre` | GET | Liste tous les genres |
| `/rayon` | GET | Liste tous les rayons |
| `/utilisateur/{json}` | GET | Vérifie les identifiants |
| `/commande` | POST | Crée une commande |
| `/exemplaire/{id}_{num}` | PUT | Modifie l'état d'un exemplaire |
