<?php
// Detectar se estamos no public/ ou app/views/
$isInPublic = strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false;

// Sistema simplificado de caminhos - sempre usar caminho absoluto
$basePath = '/Honra-e-Sombra/public/';
if ($isInPublic) {
    $basePath = '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="honra">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema completo de RPG Honra e Sombra - Crie personagens, explore classes orientais e viva aventuras épicas">
    <meta name="keywords" content="RPG, Honra e Sombra, sistema de jogo, classes orientais, samurai, ninja">
    <meta name="author" content="Sistema Honra e Sombra">
    
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Honra e Sombra RPG</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= $basePath ?>img/logo-honra-sombra.png">
    <link rel="shortcut icon" type="image/png" href="<?= $basePath ?>img/logo-honra-sombra.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Sistema de Temas Simplificado -->
    <link rel="stylesheet" href="<?= $basePath ?>css/themes.css">
    
    <!-- Animações CSS -->
    <link rel="stylesheet" href="<?= $basePath ?>css/animations.css">
    
    <!-- Toast CSS -->
    <link rel="stylesheet" href="<?= $basePath ?>js/toast.css">
    
    <!-- Google Fonts - RPG Themed -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Crimson+Text:ital,wght@0,400;0,600;1,400&family=Uncial+Antiqua&display=swap" rel="stylesheet">
</head>
<body class="bg-background text-text min-h-screen transition-colors duration-300">