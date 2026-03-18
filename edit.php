<?php
// edit.php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

// Получаем актуальные данные пользователя
try {
    // Данные пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    // Языки пользователя
    $stmt = $pdo->prepare("
        SELECT pl.name 
        FROM user_languages ul
        JOIN programming_languages pl ON ul.language_id = pl.id
        WHERE ul.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $userLanguages = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    error_log("Edit error: " . $e->getMessage());
    header('Location: logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование данных</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 300;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .user-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #fafafa;
        }
        
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            padding: 10px 0;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .radio-option input[type="radio"] {
            width: auto;
            margin: 0;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        select[multiple] {
            height: 150px;
            background: #fafafa;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 20px;
        }
        
        .btn:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-danger {
            background: #dc3545;
            margin-top: 10px;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2e7d32;
            text-align: center;
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }
        
        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .nav-link {
            color: #667eea;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-link:hover {
            background: #f0f0f0;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Редактирование данных</h1>
        
        <div class="user-info">
            <strong>Вы вошли как:</strong> <?= htmlspecialchars($_SESSION['login']) ?>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message">
                ✓ Данные успешно обновлены!
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error-message">
                ✗ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form action="update.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="full_name">ФИО *</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?= htmlspecialchars($user['full_name']) ?>"
                           placeholder="Иванов Иван Иванович"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= htmlspecialchars($user['phone']) ?>"
                           placeholder="+7 (999) 123-45-67"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>"
                           placeholder="example@mail.com"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="birth_date">Дата рождения *</label>
                    <input type="date" 
                           id="birth_date" 
                           name="birth_date" 
                           value="<?= htmlspecialchars($user['birth_date']) ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label>Пол *</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" 
                                   name="gender" 
                                   value="male"
                                   <?= $user['gender'] === 'male' ? 'checked' : '' ?>
                                   required> Мужской
                        </label>
                        <label class="radio-option">
                            <input type="radio" 
                                   name="gender" 
                                   value="female"
                                   <?= $user['gender'] === 'female' ? 'checked' : '' ?>
                                   required> Женский
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="languages">Любимый язык программирования *</label>
                    <select name="languages[]" 
                            id="languages" 
                            multiple 
                            size="6"
                            required>
                        <?php
                        $allLanguages = [
                            'Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python',
                            'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'
                        ];
                        foreach ($allLanguages as $lang):
                        ?>
                            <option value="<?= $lang ?>" 
                                <?= in_array($lang, $userLanguages) ? 'selected' : '' ?>>
                                <?= $lang ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: #777; display: block; margin-top: 5px;">
                        Удерживайте Ctrl (Cmd) для выбора нескольких
                    </small>
                </div>
                
                <div class="form-group full-width">
                    <label for="biography">Биография</label>
                    <textarea id="biography" 
                              name="biography" 
                              placeholder="Расскажите о себе..."><?= htmlspecialchars($user['biography'] ?? '') ?></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn">Сохранить изменения</button>
        </form>
        
        <div class="nav-links">
            <a href="index.php" class="nav-link">← На главную</a>
            <a href="logout.php" class="nav-link" style="color: #dc3545;">Выйти</a>
        </div>
    </div>
</body>
</html>
