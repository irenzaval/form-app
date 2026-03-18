<?php
// index.php
$errors = [];
$old = [];

// Читаем Cookies с ошибками (если есть)
if (isset($_COOKIE['form_errors'])) {
    $errors = json_decode($_COOKIE['form_errors'], true);
    // Удаляем cookie ошибок после прочтения (до конца сессии - значит при закрытии браузера)
    setcookie('form_errors', '', time() - 3600, '/');
}

// Читаем сохраненные данные из Cookies (если есть)
if (isset($_COOKIE['form_data'])) {
    $old = json_decode($_COOKIE['form_data'], true);
}

// Если есть временные данные из запроса (приоритет над Cookies)
if (isset($_GET['old'])) {
    $old = array_merge($old, json_decode(urldecode($_GET['old']), true) ?: []);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрационная форма</title>
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
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 0.9rem;
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
        
        select[multiple] option {
            padding: 8px 12px;
            margin: 2px 0;
            border-radius: 4px;
        }
        
        select[multiple] option:checked {
            background: #667eea linear-gradient(0deg, #667eea 0%, #667eea 100%);
            color: white;
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
        
        .error-container {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }
        
        .error-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #fcd;
        }
        
        .error-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .error-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .error-message {
            color: #c33;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .field-error {
            border-color: #c33 !important;
            background: #fff0f0 !important;
        }
        
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2e7d32;
            text-align: center;
            font-weight: 500;
        }
        
        .hint {
            color: #777;
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
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
        <h1>Регистрационная форма</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-container">
                <strong>Пожалуйста, исправьте следующие ошибки:</strong>
                <?php foreach ($errors as $field => $error): ?>
                    <div class="error-item">
                        <div class="error-title">Поле "<?= htmlspecialchars($field) ?>":</div>
                        <div class="error-message"><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="success-message">
                ✓ Данные успешно сохранены! ID записи: <?= htmlspecialchars($_GET['id'] ?? '') ?>
            </div>
        <?php endif; ?>
        
        <form action="save.php" method="POST">
            <div class="form-grid">
                <div class="form-group <?= isset($errors['full_name']) ? 'has-error' : '' ?>">
                    <label for="full_name">ФИО *</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                           placeholder="Иванов Иван Иванович"
                           class="<?= isset($errors['full_name']) ? 'field-error' : '' ?>"
                           required>
                    <?php if (isset($errors['full_name'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['full_name']) ?></div>
                    <?php endif; ?>
                    <span class="hint">Допустимы только буквы, пробелы и дефисы</span>
                </div>
                
                <div class="form-group <?= isset($errors['phone']) ? 'has-error' : '' ?>">
                    <label for="phone">Телефон *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                           placeholder="+7 (999) 123-45-67"
                           class="<?= isset($errors['phone']) ? 'field-error' : '' ?>"
                           required>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                    <span class="hint">Допустимы цифры, пробелы, +, -, (, )</span>
                </div>
                
                <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                    <label for="email">E-mail *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                           placeholder="example@mail.com"
                           class="<?= isset($errors['email']) ? 'field-error' : '' ?>"
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                    <span class="hint">Формат: user@domain.com</span>
                </div>
                
                <div class="form-group <?= isset($errors['birth_date']) ? 'has-error' : '' ?>">
                    <label for="birth_date">Дата рождения *</label>
                    <input type="date" 
                           id="birth_date" 
                           name="birth_date" 
                           value="<?= htmlspecialchars($old['birth_date'] ?? '') ?>"
                           class="<?= isset($errors['birth_date']) ? 'field-error' : '' ?>"
                           required>
                    <?php if (isset($errors['birth_date'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['birth_date']) ?></div>
                    <?php endif; ?>
                    <span class="hint">Формат: ГГГГ-ММ-ДД</span>
                </div>
                
                <div class="form-group <?= isset($errors['gender']) ? 'has-error' : '' ?>">
                    <label>Пол *</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" 
                                   name="gender" 
                                   value="male"
                                   <?= (isset($old['gender']) && $old['gender'] === 'male') ? 'checked' : '' ?>
                                   required> Мужской
                        </label>
                        <label class="radio-option">
                            <input type="radio" 
                                   name="gender" 
                                   value="female"
                                   <?= (isset($old['gender']) && $old['gender'] === 'female') ? 'checked' : '' ?>
                                   required> Женский
                        </label>
                    </div>
                    <?php if (isset($errors['gender'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['gender']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group <?= isset($errors['languages']) ? 'has-error' : '' ?>">
                    <label for="languages">Любимый язык программирования *</label>
                    <select name="languages[]" 
                            id="languages" 
                            multiple 
                            size="6"
                            class="<?= isset($errors['languages']) ? 'field-error' : '' ?>"
                            required>
                        <?php
                        $languages = [
                            'Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python',
                            'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'
                        ];
                        $selectedLanguages = $old['languages'] ?? [];
                        foreach ($languages as $lang):
                        ?>
                            <option value="<?= $lang ?>" 
                                <?= in_array($lang, $selectedLanguages) ? 'selected' : '' ?>>
                                <?= $lang ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['languages'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['languages']) ?></div>
                    <?php endif; ?>
                    <small class="hint">
                        Удерживайте Ctrl (Cmd) для выбора нескольких
                    </small>
                </div>
                
                <div class="form-group full-width <?= isset($errors['biography']) ? 'has-error' : '' ?>">
                    <label for="biography">Биография</label>
                    <textarea id="biography" 
                              name="biography" 
                              placeholder="Расскажите о себе..."
                              class="<?= isset($errors['biography']) ? 'field-error' : '' ?>"><?= htmlspecialchars($old['biography'] ?? '') ?></textarea>
                    <?php if (isset($errors['biography'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['biography']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group full-width <?= isset($errors['contract_accepted']) ? 'has-error' : '' ?>">
                    <div class="checkbox-group">
                        <input type="checkbox" 
                               name="contract_accepted" 
                               id="contract" 
                               value="1"
                               <?= isset($old['contract_accepted']) ? 'checked' : '' ?>
                               required>
                        <label for="contract">Я ознакомлен(а) с контрактом *</label>
                    </div>
                    <?php if (isset($errors['contract_accepted'])): ?>
                        <div class="error-message"><?= htmlspecialchars($errors['contract_accepted']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <button type="submit" class="btn">Сохранить</button>
        </form>
    </div>
</body>
</html>
