async function loadFeed() {
    try {
        const response = await fetch('/api/ideas'); // Sua Rota A
        const ideas = await response.json();
        renderFeed(ideas);
    } catch (error) {
        console.error("Erro ao navegar nessas águas:", error);
    }
}

function renderFeed(ideas) {
    const app = document.getElementById('feed-app');
    
    app.innerHTML = ideas.map(idea => `
        <article class="bg-main border rounded-lg shadow-sm mb-6 post-card-transition"> 
            <div class="p-4">
                <header class="flex items-center gap-4 mb-4">
                    <div class="volt-avatar-sm">${idea.author.avatar}</div>
                    <div>
                        <div class="text-main font-bold">${idea.author.name}</div>
                        <div class="text-muted text-sm">${idea.created_at}</div>
                    </div>
                </header>
                
                <div class="text-main mb-4">${idea.content}</div>
                
                <!-- Container de Comentários (Agora com classe específica do feed) -->
                <section class="feed-comments-box rounded-md">
                    ${idea.comments.map(comment => `
                        <div class="feed-comment-item p-2 text-sm text-main">
                            <span class="font-bold">${comment.author}:</span> ${comment.content}
                        </div>
                    `).join('')}
                </section>
            </div>
        </article>
    `).join('');
}

document.addEventListener('DOMContentLoaded', loadFeed);