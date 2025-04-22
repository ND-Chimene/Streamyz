# ⚙️ Installation de Streamyz avec Docker

Ce guide vous explique comment installer et exécuter le projet **Streamyz**, une plateforme de type Allociné développée avec Symfony, dans un environnement Dockerisé avec Nginx, PHP, MySQL, PhpMyAdmin et un fetcher TMDB.

---

## 🧱 Services utilisés

| Service        | Description                                      |
|----------------|--------------------------------------------------|
| PHP-FPM        | Exécution de l’application Symfony               |
| Nginx          | Serveur HTTP frontal                             |
| MySQL          | Base de données relationnelle                    |
| PhpMyAdmin     | Interface de gestion MySQL via navigateur        |
| TMDB Fetcher   | Service pour interagir avec l’API TheMovieDB     |

---

## 🔧 Prérequis

- Docker & Docker Compose installés
- Symfony CLI (facultatif mais recommandé)
- Fichier `.env.local` configuré à la racine du projet

---

## 🚀 Lancer l’environnement Docker

1. **Cloner le dépôt** :

```bash
git clone https://github.com/ton-utilisateur/streamyz.git
cd streamyz
```

2. **Configurer votre fichier `.env.local`** :

Assurez-vous d’avoir un fichier `.env.local` contenant vos variables sensibles (ex : clé TMDB).

3. **Lancer les conteneurs** :

```bash
docker-compose up --build -d
```

4. **Vérifier que les conteneurs sont actifs** :

```bash
docker-compose ps
```

---

## 🌐 Accès aux interfaces

- Application Symfony : [http://localhost:8000](http://localhost:8000)
- PhpMyAdmin : [http://localhost:8081](http://localhost:8081)

---

## 🧰 Commandes utiles

- **Accéder au conteneur PHP** :

```bash
docker exec -it streamyz_php_1 bash
```

- **Lancer les commandes Symfony** (dans le conteneur) :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

- **Recréer l’environnement Docker si besoin** :

```bash
docker-compose down -v
docker-compose up --build -d
```

---

## 🏁 Conclusion

Cette configuration Docker permet d’isoler l’environnement de développement, de faciliter la gestion des services et de garantir la stabilité de l’application Streamyz en local. L'utilisation du fetcher dédié TMDB permet de simuler ou automatiser les appels à l’API TheMovieDB pour enrichir l’expérience de développement.