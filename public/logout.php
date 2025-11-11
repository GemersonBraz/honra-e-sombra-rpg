<?php
/**
 * Arquivo de Logout Direto - Sistema Honra e Sombra RPG
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/models/User.php';

if (isLoggedIn()) {
    $user = new User();
    $user->logout();
    setMessage('Logout realizado com sucesso! Até a próxima aventura!', 'info', [
        'title' => 'Logout Realizado'
    ]);
}

// Redirecionar para página inicial
header("Location: index.php");
exit;
?>