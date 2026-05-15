# Déploiement de FacturePro sur Vercel

## 📋 Prérequis
1. Compte Vercel gratuit : https://vercel.com/signup
2. Compte GitHub (recommandé) : https://github.com
3. Git installé sur votre machine

## 🚀 Étapes de déploiement

### Étape 1 : Initialiser Git
```bash
cd tp_php01
git init
git add .
git commit -m "Initial commit - Application FacturePro"
```

### Étape 2 : Créer un dépôt GitHub
1. Allez sur https://github.com
2. Cliquez sur "+" → "New repository"
3. Nommez-le (ex: `facturepro`)
4. Ne cochez PAS "Initialize with README"
5. Suivez les instructions pour pousser votre code :

```bash
git remote add origin https://github.com/votre-username/facturepro.git
git branch -M main
git push -u origin main
```

### Étape 3 : Déployer sur Vercel
1. Connectez-vous à https://vercel.com
2. Cliquez sur "Add New..." → "Project"
3. Importez votre dépôt GitHub
4. Vercel détectera automatiquement la configuration PHP
5. Cliquez sur "Deploy"

## ⚙️ Configuration Vercel

### Paramètres recommandés :
- **Framework Preset** : Other
- **Build Command** : (laisser vide)
- **Output Directory** : (laisser vide)
- **Install Command** : (laisser vide)

### Variables d'environnement (optionnel) :
```
VERCEL=1
```

## 🌐 Accès à l'application

Une fois déployé, vous recevrez une URL comme :
```
https://facturepro.vercel.app
```

Vous pourrez y accéder depuis :
- Votre ordinateur
- Votre téléphone
- N'importe quel appareil connecté à internet

## ⚠️ Limitations importantes

### 1. **Stockage des fichiers**
Sur Vercel, les fichiers sont stockés dans `/tmp` qui est :
- **Temporaire** : Les fichiers peuvent être effacés
- **Non persistant** entre les déploiements
- **Partagé** entre toutes les instances

**Solution** : Pour une app de production, migrez vers :
- Base de données MySQL/PostgreSQL
- Service de stockage comme AWS S3
- Base de données serverless comme Supabase

### 2. **Sessions PHP**
Les sessions PHP ne persistent pas sur Vercel.

**Solution** : Utilisez des cookies ou migrez vers une authentification JWT.

### 3. **Performances**
Vercel utilise des fonctions serverless (cold starts).

## 🔧 Pour le développement

### Localement avec PHP intégré :
```bash
php -S localhost:3000
```

### Avec XAMPP :
Placez le dossier dans `htdocs` et accédez à :
```
http://localhost/tp_php01
```

## 📞 Support
- Documentation Vercel PHP : https://vercel.com/docs/functions/serverless-functions/runtimes/php
- GitHub du projet : Votre dépôt
- Pour des questions : Consultez la documentation Vercel

## 🎯 Prochaines étapes recommandées
1. Déployer sur Vercel pour tester
2. Migrer les données vers une base de données
3. Implémenter l'authentification JWT
4. Ajouter un système de backup des données