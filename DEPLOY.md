# Déploiement sur Vercel

## Prérequis
1. Compte Vercel (gratuit) : https://vercel.com
2. Git installé sur votre machine
3. Node.js (optionnel pour les commandes)

## Étapes de déploiement

### 1. Initialiser Git
```bash
git init
git add .
git commit -m "Initial commit"
```

### 2. Créer un dépôt sur GitHub
- Allez sur https://github.com
- Créez un nouveau dépôt
- Suivez les instructions pour pousser votre code

### 3. Déployer sur Vercel
1. Connectez-vous à Vercel
2. Cliquez sur "New Project"
3. Importez votre dépôt GitHub
4. Vercel détectera automatiquement la configuration
5. Cliquez sur "Deploy"

### 4. Configuration manuelle (si nécessaire)
- **Framework Preset** : Other
- **Build Command** : (laisser vide)
- **Output Directory** : (laisser vide)
- **Install Command** : (laisser vide)

## Notes importantes

### Limitations sur Vercel
1. **Sessions** : Les sessions PHP ne persistent pas entre les déploiements
2. **Fichiers** : Les fichiers dans `/tmp` sont temporaires
3. **Base de données** : Pour une app de production, utilisez une base de données externe

### Pour une solution plus stable
Considérez ces alternatives :
1. **Railway** : Supporte PHP natif
2. **Heroku** : Avec buildpack PHP
3. **AlwaysData** : Hébergement PHP spécialisé

## Accès à l'application
Une fois déployé, vous recevrez une URL comme :
```
https://votre-app.vercel.app
```

## Développement local
```bash
# Avec PHP intégré
php -S localhost:3000

# Ou avec XAMPP
# Placez le dossier dans htdocs et accédez à http://localhost/tp_php01
```