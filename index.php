<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрационная форма</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 800px;
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
            font-weight: 600;
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
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .radio-label input[type="radio"] {
            width: auto;
            margin-right: 5px;
        }

        .checkbox-group {
            margin: 20px 0;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            width: auto;
        }

        select[multiple] {
            height: 150px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .error-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
        }

        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
        }

        .error-list {
            list-style: none;
            margin-top: 10px;
        }

        .error-list li {
            margin-bottom: 5px;
        }

        .required:after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }

        .hint {
            color: #666;
            font-size: 0.85em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Регистрационная форма</h1>
        
        <?php
        session_start();
        if (isset($_SESSION['errors'])) {
            echo '<div class="error-message">';
            echo '<strong>Пожалуйста, исправьте следующие ошибки:</strong>';
            echo '<ul class="error-list">';
            foreach ($_SESSION['errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            unset($_SESSION['errors']);
        }
        
        if (isset($_SESSION['success'])) {
            echo '<div class="success-message">';
            echo '<strong>' . htmlspecialchars($_SESSION['success']) . '</strong>';
            echo '</div>';
            unset($_SESSION['success']);
        }
        ?>

        <form action="process_form.php" method="POST">
            <div class="form-group">
                <label for="full_name" class="required">ФИО</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?php echo isset($_SESSION['form_data']['full_name']) ? htmlspecialchars($_SESSION['form_data']['full_name']) : ''; ?>" 
                       placeholder="Иванов Иван Иванович" required>
                <div class="hint">Только буквы и пробелы, не более 150 символов</div>
            </div>

            <div class="form-group">
                <label for="phone" class="required">Телефон</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?php echo isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : ''; ?>" 
                       placeholder="+7 (999) 123-45-67" required>
            </div>

            <div class="form-group">
                <label for="email" class="required">E-mail</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" 
                       placeholder="example@mail.com" required>
            </div>

            <div class="form-group">
                <label for="birth_date" class="required">Дата рождения</label>
                <input type="date" id="birth_date" name="birth_date" 
                       value="<?php echo isset($_SESSION['form_data']['birth_date']) ? htmlspecialchars($_SESSION['form_data']['birth_date']) : ''; ?>" 
                       required>
            </div>

            <div class="form-group">
                <label class="required">Пол</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="gender" value="male" 
                               <?php echo (isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'male') ? 'checked' : ''; ?> 
                               required> Мужской
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="gender" value="female" 
                               <?php echo (isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'female') ? 'checked' : ''; ?> 
                               required> Женский
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="programming_languages" class="required">Любимый язык программирования</label>
                <select name="programming_languages[]" id="programming_languages" multiple required>
                    <?php
                    $languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];
                    $selected_langs = isset($_SESSION['form_data']['programming_languages']) ? $_SESSION['form_data']['programming_languages'] : [];
                    
                    foreach ($languages as $lang) {
                        $selected = in_array($lang, $selected_langs) ? 'selected' : '';
                        echo "<option value=\"$lang\" $selected>$lang</option>";
                    }
                    ?>
                </select>
                <div class="hint">Для выбора нескольких языков удерживайте Ctrl (Cmd на Mac)</div>
            </div>

            <div class="form-group">
                <label for="biography">Биография</label>
                <textarea id="biography" name="biography" placeholder="Расскажите о себе..."><?php echo isset($_SESSION['form_data']['biography']) ? htmlspecialchars($_SESSION['form_data']['biography']) : ''; ?></textarea>
            </div>

            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="contract_accepted" value="1" 
                           <?php echo isset($_SESSION['form_data']['contract_accepted']) ? 'checked' : ''; ?> 
                           required>
                    <span class="required">Я ознакомлен(а) с контрактом</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">Сохранить</button>
        </form>
    </div>
</body>
</html>
<?php
// Очищаем временные данные после отображения
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>