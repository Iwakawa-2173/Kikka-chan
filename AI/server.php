<?php

// Простой обработчик сообщений для демонстрации.
// В реальном приложении вы можете использовать более сложные методы обработки,
// такие как интеграция с AI или базой данных для хранения истории чата.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $received_message = trim($_POST['message']);

    // Простая логика ответов бота.
    switch ($received_message) { 
      case "Привет":
          echo "Здравствуйте!";
          break;  
      case "Как дела?":
          echo "Хорошо!";
          break;  
      default:
          echo "Извините, не понял ваш вопрос.";
          break;  
     }

} else { 
     http_response_code(405); // Method Not Allowed
}

?>
