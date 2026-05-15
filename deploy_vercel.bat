@echo off
echo === Déploiement FacturePro sur Vercel ===
echo.

echo 1. Vérification des prérequis...
where vercel >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Vercel CLI non installé
    echo Installez-le avec: npm install -g vercel
    pause
    exit /b 1
)

where git >nul 2>nul
if %errorlevel% neq 0 (
    echo ⚠ Git non installé (recommandé)
)

echo ✓ Vercel CLI détecté
echo.

echo 2. Vérification des fichiers...
if not exist "api\router.php" (
    echo ❌ Fichier router.php manquant
    pause
    exit /b 1
)

if not exist "vercel.json" (
    echo ❌ Fichier vercel.json manquant
    pause
    exit /b 1
)

echo ✓ Tous les fichiers nécessaires présents
echo.

echo 3. Initialisation des données...
php init-vercel.php
echo.

echo 4. Déploiement sur Vercel...
echo.
echo Options:
echo   1. Déploiement de test (preview)
echo   2. Déploiement en production
echo   3. Annuler
echo.

set /p choice="Choisissez une option [1-3]: "

if "%choice%"=="1" (
    echo Déploiement en mode preview...
    vercel
) else if "%choice%"=="2" (
    echo Déploiement en production...
    vercel --prod
) else (
    echo Déploiement annulé
    pause
    exit /b 0
)

echo.
echo === Déploiement terminé ===
echo.
echo URLs d'accès:
echo - Accueil: https://votre-app.vercel.app/
echo - Test API: https://votre-app.vercel.app/api/index.php
echo - Santé: https://votre-app.vercel.app/api/health.php
echo.
pause