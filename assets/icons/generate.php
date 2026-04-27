<?php
// Génère icon-192.png et icon-512.png à partir d'un SVG inline
// Accéder via : http://localhost/tp_php01/assets/icons/generate.php

function svgToPng($taille, $dest) {
    // SVG simple avec fond bleu et texte FP
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$taille}" height="{$taille}" viewBox="0 0 {$taille} {$taille}">
  <rect width="{$taille}" height="{$taille}" rx="{$taille}" fill="#1d6fa4"/>
  <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle"
        font-family="Arial,sans-serif" font-weight="bold"
        font-size="{$taille}" fill="white">🛒</text>
</svg>
SVG;
    // Sauvegarder le SVG (fallback si pas de GD)
    file_put_contents($dest . '.svg', $svg);

    if (!extension_loaded('gd')) {
        echo "GD non disponible. SVG créé : $dest.svg<br>";
        return;
    }

    $img = imagecreatefrompng('data:image/png;base64,' . base64_encode($svg));
    imagepng($img, $dest);
    imagedestroy($img);
    echo "PNG créé : $dest<br>";
}

svgToPng(192, __DIR__ . '/icon-192.png');
svgToPng(512, __DIR__ . '/icon-512.png');
