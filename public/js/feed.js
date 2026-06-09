// Toggle user menu
function toggleMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('active');
}

// Handle file selection
function handleFileSelect() {
    const file = document.getElementById('fileInput').files[0];
    if (file) {
        document.getElementById('fileName').textContent = '📎 ' + file.name;
    }
}

// Post form submission
document.getElementById('postForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const content = document.getElementById('postContent').value.trim();
    const fileInput = document.getElementById('fileInput');

    if (!content) {
        showMessage('Post content cannot be empty', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('content', content);

    if (fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }

    try {
        const response = await fetch('../api/posts.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showMessage('Post created successfully', 'success');
            document.getElementById('postForm').reset();
            document.getElementById('fileName').textContent = '';
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred', 'error');
    }
});

// Toggle like
async function toggleLike(postId) {
    const btn = document.querySelector(`.like-btn[data-post-id="${postId}"]`);

    try {
        const action = btn.classList.contains('liked') ? 'unlike' : 'like';
        const formData = new FormData();
        formData.append('action', action);
        formData.append('post_id', postId);

        const response = await fetch('../api/posts.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            btn.classList.toggle('liked');
            btn.querySelector('.like-count').textContent = data.likes_count;
        }
    } catch (error) {
        showMessage('An error occurred', 'error');
    }
}

// Show comments (placeholder)
function showComments(postId) {
    alert('Comments feature coming soon!');
}

// Delete post
async function deletePost(postId) {
    if (!confirm('Are you sure you want to delete this post?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('post_id', postId);

    try {
        const response = await fetch('../api/posts.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showMessage('Post deleted', 'success');
            document.querySelector(`.post[data-post-id="${postId}"]`).remove();
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred', 'error');
    }
}

// Logout
async function logout() {
    const formData = new FormData();
    formData.append('action', 'logout');

    try {
        const response = await fetch('../api/auth.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = 'index.php';
        }
    } catch (error) {
        showMessage('An error occurred', 'error');
    }
}

// Show message
function showMessage(message, type) {
    const messageEl = document.getElementById('message');
    messageEl.textContent = message;
    messageEl.className = `message show ${type}`;

    setTimeout(() => {
        messageEl.classList.remove('show');
    }, 3000);
}
