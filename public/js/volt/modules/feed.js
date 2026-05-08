async function loadFeed() {
    try {
        // 1. Pegamos o idioma que está na URL atual (ex: 'pt' ou 'en')
        // Uma forma simples é pegar o primeiro segmento após a barra
        const lang = window.location.pathname.split('/')[1] || 'en';

        // 2. Montamos a URL da API com o prefixo do idioma
        const response = await fetch(`/${lang}/api/ideas`); 
        
        const ideas = await response.json();
        renderFeed(ideas);
    } catch (error) {
        console.error("Erro ao navegar nessas águas:", error);
    }
}

function formatRelativeTime(dateString) {
    // Tratamos a string para garantir compatibilidade com o construtor Date
    const past = new Date(dateString.replace(' ', 'T'));
    const now = new Date();
    const diffInSeconds = Math.floor((now - past) / 1000);

    if (diffInSeconds < 60) return "agora"; // Simplificando a i18n por enquanto
    
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes}min`;
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours}h`;
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays}d`;

    return past.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' }).replace('.', '');
}

function renderStars(rating) {    
    const numericRating = parseInt(rating) || 0;    
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        const activeClass = i <= numericRating ? 'star-primary' : 'star-muted';
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
                        <div class="text-muted text-sm">${formatRelativeTime(idea.created_at)}</div>
                    </div>
                </header>
                
                <div class="text-main mb-4">${idea.content}</div>
                
                <section class="feed-comments-box rounded-md bg-subtle p-2">
                    ${(idea.comments || []).map(comment => `
                        <div class="feed-comment-item mb-3 text-sm flex gap-2">
                            <div class="volt-avatar-xs">
                                ${comment.avatar ? `<img src="${comment.avatar}">` : comment.author[0]}
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <span class="font-bold text-primary">${comment.author}</span>
                                    <span class="text-muted text-xs">${formatRelativeTime(comment.created_at)}</span>
                                </div>
                                <p class="text-main">${comment.content}</p>
                                <div class="mt-1">${renderStars(comment.rating)}</div>
                            </div>
                        </div>
                    `).join('')}
                </section>
            </div>
        </article>
        `;
    }).join('');
}

document.addEventListener('DOMContentLoaded', loadFeed);