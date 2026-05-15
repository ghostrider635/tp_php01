#!/bin/bash
echo "=== Déploiement FacturePro sur Vercel ==="
echo

echo "1. Vérification des prérequis..."
if ! command -v vercel &> /dev/null; then
    echo "❌ Vercel CLI non installé"
    echo "Installez-le avec: npm install -g vercel"
    exit 1
fi

if ! command -v git &> /dev/null; then
    echo "⚠ Git non installé (recommandé)"
fi

echo "✓ Vercel CLI détecté"
echo

echo "2. Vérification des fichiers..."
if [ ! -f "api/router.php" ]; then
    echo "❌ Fichier router.php manquant"
    exit 1
fi

if [ ! -f "vercel.json" ]; then
    echo "❌ Fichier vercel.json manquant"
    exit 1
fi

echo "✓ Tous les fichiers nécessaires présents"
echo

echo "3. Initialisation des données..."
php init-vercel.php
echo

echo "4. Déploiement sur Vercel..."
echo
echo "Options:"
echo "  1. Déploiement de test (preview)"
echo "  2. Déploiement en production"
echo "  3. Annuler"
echo

read -p "Choisissez une option [1-3]: " choice

case $choice in
    1)
        echo "Déploiement en mode preview..."
        vercel
        ;;
    2)
        echo "Déploiement en production..."
        vercel --prod
        ;;
    3)
        echo "Déploiement annulé"
        exit 0
        ;;
    *)
        echo "Option invalide"
        exit 1
        ;;
esac

echo
echo "=== Déploiement terminé ==="
echo
echo "URLs d'accès:"
echo "- Accueil: https://votre-app.vercel.app/"
echo "- Test API: https://votre-app.vercel.app/api/index.php"
echo "- Santé: https://votre-app.vercel.app/api/health.php"
echo