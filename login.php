<?php
// login.php
session_start();

// Если уже есть активная сессия, перенаправляем на редактирование
if (isset($_SESSION['user_id']) && isset($_SESSION['authenticated'])) {
    header('Location: edit.php');
    exit;
}

$error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 300;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            font-weight: 600;
        }
        
        .btn:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
            text-align: center;
        }
        
        .info {
            background: #e3f2fd;
            color: #1976d2;
            padding: 12px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Вход для редактирования</h1>
        
        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
       <form action="auth.php" method="POST">
    <div class="form-group">
        <label for="login">Логин</label>
        <input type="text" 
               id="login" 
               name="login"           <!-- ВАЖНО: name="login" -->
               placeholder="Введите логин"
               required>
    </div>
    
    <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" 
               id="password" 
               name="password"        <!-- ВАЖНО: name="password" -->
               placeholder="Введите пароль"
               required>
    </div>
    
    <button type="submit">Войти</button>
</form>
            </div>
            
           
