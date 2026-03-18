<?php
// auth.php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($login) || empty($password)) {
    header('Location: login.php?error=' . urlencode('Введите логин и пароль'));
    exit;
}

try {
    // Ищем пользователя по логину
    $stmt = $pdo->prepare("
        SELECT ua.*, u.full_name, u.email, u.phone, u.birth_date, u.gender, u.biography
        FROM user_accounts ua
        JOIN users u ON ua.user_id = u.id
        WHERE ua.login = ?
    ");
    $stmt->execute([$login]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Успешный вход
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['authenticated'] = true;
        
        // Загружаем данные пользователя для формы редактирования
        $_SESSION['edit_data'] = [
            'full_name' => $user['full_name'],
            'phone' => $user['phone'],
            'email' => $user['email'],
            'birth_date' => $user['birth_date'],
            'gender' => $user['gender'],
            'biography' => $user['biography']
        ];
        
        // Загружаем языки пользователя
        $stmt = $pdo->prepare("
            SELECT pl.name 
            FROM user_languages ul
            JOIN programming_languages pl ON ul.language_id = pl.id
            WHERE ul.user_id = ?
        ");
        $stmt->execute([$user['user_id']]);
        $languages = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $_SESSION['edit_data']['languages'] = $languages;
        
        header('Location: edit.php');
        exit;
    } else {
        header('Location: login.php?error=' . urlencode('Неверный логин или пароль'));
        exit;
    }
} catch (Exception $e) {
    error_log("Auth error: " . $e->getMessage());
    header('Location: login.php?error=' . urlencode('Ошибка при входе'));
    exit;
}
?>
