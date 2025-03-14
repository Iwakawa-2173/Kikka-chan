async function loadThreads() {
    const response = await fetch('/api/b/threads');
    const posts = await response.json();
    
    let html = '';
    posts.forEach(post => {
        html += `
        <div class="post">
            ${post.image ? `<img src="/img/thumb/${post.image}" class="thumb">` : ''}
            <div class="message">${post.message.replace(/\n/g, '<br>')}</div>
            <small class="text-muted">#${post.id}</small>
        </div>`;
    });
    
    document.getElementById('posts').innerHTML = html;
}

async function submitPost(e) {
    e.preventDefault();
    const message = document.getElementById('message').value;
    const file = document.getElementById('image').files[0];
    
    let imageBase64 = '';
    if (file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        imageBase64 = await new Promise(resolve => {
            reader.onload = () => resolve(reader.result);
        });
    }

    try {
        const response = await fetch('/api/b/post', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ message, image: imageBase64 })
        });
        
        if (!response.ok) throw await response.json();
        
        document.getElementById('message').value = '';
        document.getElementById('image').value = '';
        loadThreads();
    } catch (err) {
        document.getElementById('error').textContent = err.error || 'Ошибка';
    }
}

// Загрузка постов при старте
loadThreads();
