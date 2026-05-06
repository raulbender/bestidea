async function loadFeed() {
    try {
        const response = await fetch('/api/ideas'); // Sua Rota A
        const ideas = await response.json();
        renderFeed(ideas);
    } catch (error) {
        console.error("Erro ao navegar nessas águas:", error);
    }
}

function renderStars(rating) {
    // Cria as 5 estrelas, pintando de dourado (classe text-primary) as que o rating alcança
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        const activeClass = i <= rating ? 'text-primary' : 'text-muted';
        starsHtml += `<span class="${activeClass}">★</span>`;
    }
    return starsHtml;
}

function renderFeed(ideas) {
    const app = document.getElementById('feed-app');

    if (ideas.length === 0) {
        app.innerHTML = '<div class="text-center p-6">Nenhuma ideia no horizonte ainda...</div>';
        return;
    }
    
    app.innerHTML = ideas.map(idea => {
        // Garantimos que o avatar tenha pelo menos a primeira letra se estiver vazio
        const avatarInitial = idea.author_name ? idea.author_name[0].toUpperCase() : '?';
        const avatarContent = idea.author_avatar ? `<img src="${idea.author_avatar}">` : avatarInitial;

        return `
        <article class="bg-main border rounded-lg shadow-sm mb-6 volt-animate"> 
            <div class="p-4">
                <header class="flex items-center gap-4 mb-4">
                    <div class="volt-avatar-sm">${avatarContent}</div>
                    <div>
                        <div class="text-main font-bold">${idea.author_name}</div>
                        <div class="text-muted text-sm">${idea.created_at}</div>
                    </div>
                </header>
                
                <div class="text-main mb-4">${idea.content}</div>
                
                <section class="feed-comments-box rounded-md bg-subtle p-2">
                    ${idea.comments.map(comment => `
                        <div class="feed-comment-item mb-1 text-sm">
                            <span class="font-bold text-primary">${comment.author}:</span> 
                            <span class="text-main">${comment.content}</span>
                            <span class="ml-2">${renderStars(comment.rating)}</span>
                        </div>
                    `).join('')}
                </section>
            </div>
        </article>
        `;
    }).join('');
}

document.addEventListener('DOMContentLoaded', loadFeed);