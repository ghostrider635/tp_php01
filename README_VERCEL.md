# Déploiement FacturePro sur Vercel

## Configuration requise

1. Compte Vercel (gratuit)
2. CLI Vercel installé : `npm i -g vercel`
3. Git pour le versioning

## Déploiement

### Première fois
```bash
cd tp_php01
vercel login
vercel
```

### Déploiements suivants
```bash
vercel --prod
```

## Structure du projet

```
api/
├── router.php          # Routeur principal
├── index.php          # Test API
├── health.php         # Endpoint santé
├── factures.php       # API factures
└── produits.php       # API produits
```

## URLs de test

- **Accueil** : `https://votre-app.vercel.app/`
- **Test API** : `https://votre-app.vercel.app/api/index.php`
- **Santé** : `https://votre-app.vercel.app/api/health.php`
- **Login** : `https://votre-app.vercel.app/auth/login.php`

## Configuration Vercel

Le projet utilise :
- Runtime PHP Vercel (`vercel-php@0.9.0`)
- Sessions stockées dans `/tmp`
- Fichiers de données dans `/tmp`
- Rewrite toutes les requêtes vers `/api/router.php`

## Dépannage

1. **Sessions ne fonctionnent pas** : Vérifier que `/tmp` est accessible en écriture
2. **Fichiers de données non chargés** : Exécuter `init-vercel.php` manuellement
3. **Routes 404** : Vérifier le mapping dans `router.php`

## Variables d'environnement

Aucune variable d'environnement requise. L'application détecte automatiquement si elle tourne sur Vercel via `$_ENV['VERCEL']`.

## Notes importantes

- Les fichiers dans `/tmp` sont persistants entre les déploiements
- Les sessions expirent après 24h d'inactivité
- L'application est compatible PWA (Progressive Web App)