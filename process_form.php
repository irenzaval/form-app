<?php
session_start();

// Включаем отображение всех ошибок PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Параметры подключения к БД
$host = 'localhost';
$dbname = 'form_app';
$username = 'root';
$password = ''; // Убедитесь, что пароль правильный (часто пустой в XAMPP)

echo "🔍 Начинаем отладку...<br><br>";

// Шаг 1: Проверяем полученные данные
echo "1️⃣ Полученные данные из формы:<br>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

try {
    // Шаг 2: Проверяем подключение к БД
    echo "2️⃣ Пытаемся подключиться к БД...<br>";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Подключение успешно!<br><br>";
    
    // Шаг 3: Проверяем валидацию
    echo "3️⃣ Проверяем валидацию...<br>";
    
    // Простейшая валидация для теста
    $errors = [];
    if (empty($_POST['full_name'])) $errors[] = "Нет ФИО";
    if (empty($_POST['email'])) $errors[] = "Нет email";
    
    if (!empty($errors)) {
        echo "❌ Ошибки валидации:<br>";
        print_r($errors);
        exit();
    }
    echo "✅ Валидация пройдена<br><br>";
    
    // Шаг 4: Пытаемся вставить данные
    echo "4️⃣ Пытаемся вставить данные в таблицу users...<br>";
    
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, phone, email, birth_date, gender, biography, contract_accepted) 
        VALUES (:full_name, :phone, :email, :birth_date, :gender, :biography, :contract_accepted)
    ");
    
    $contract_accepted = isset($_POST['contract_accepted']) ? 1 : 0;
    
    $result = $stmt->execute([
        ':full_name' => $_POST['full_name'] ?? '',
        ':phone' => $_POST['phone'] ?? '',
        ':email' => $_POST['email'] ?? '',
        ':birth_date' => $_POST['birth_date'] ?? '',
        ':gender' => $_POST['gender'] ?? '',
        ':biography' => $_POST['biography'] ?? '',
        ':contract_accepted' => $contract_accepted
    ]);
    
    if ($result) {
        $userId = $pdo->lastInsertId();
        echo "✅ Данные успешно вставлены! ID записи: $userId<br><br>";
        
        // Шаг 5: Проверяем, что данные действительно сохранились
        echo "5️⃣ Проверяем сохраненные данные:<br>";
        $check = $pdo->query("SELECT * FROM users WHERE id = $userId")->fetch(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($check);
        echo "</pre>";
        
    } else {
        echo "❌ Ошибка при вставке данных<br>";
        print_r($stmt->errorInfo());
    }
    
} catch(PDOException $e) {
    echo "❌ Ошибка БД: " . $e->getMessage() . "<br>";
    echo "Код ошибки: " . $e->getCode();
}
?>