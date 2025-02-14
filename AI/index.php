<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="chat-container" id="chatContainer">
    <!-- Сообщения будут добавляться сюда -->
</div>
<div class="center-input">
	<input class="inputMessage" type="text" id="inputMessage" placeholder="Введите сообщение...">
	<button class="send-button" onclick="sendMessage()">Отправить</button>
</div>


<script src="jquery-3.6.0.js"></script>

<script>
    document.getElementById('inputMessage').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
            e.preventDefault(); // Предотвращаем стандартное поведение браузера
        }
    });
    function sendMessage() {
        let message = $('#inputMessage').val();
        
        if (message.trim() !== '') {
            $.ajax({
                type:'POST',
                url:'server.php',
                data:{'message': message},
                success:function(data){
                    addMessage(message, 'user');
                    addMessage(data.trim(), 'bot');
                    $('#inputMessage').val('');
                },
                error:function(xhr,status,error){
                    console.log('Ошибка:', error);
                }
           });
       } else {
           alert('Пожалуйста, введите сообщение.');
       }
   }

   function addMessage(text, sender) {
       let container = document.getElementById('chatContainer');
       let div = document.createElement('div');
       div.className = 'message';
       
       if (sender === 'user') {
           div.innerHTML = `<span class='message-user'>Вы:</span> ${text}`;
       } else if (sender === 'bot') {
           div.innerHTML = `<span class='message-bot'>YAAI:</span> ${text}`;
       }

       container.appendChild(div);
   }

   // Инициализация чата
   $(document).ready(function(){
      // Если нужно что-то сделать при загрузке страницы
   });
</script>

</body>
</html>
