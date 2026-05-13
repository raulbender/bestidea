async function loadFeed() {
    try {
        // 1. Pegamos o idioma que está na URL atual (ex: 'pt' ou 'en')
        // Uma forma simples é pegar o primeiro segmento após a barra
        const lang = window.location.pathname.split('/')[1] || window.VoltI18n.lang || 'en';
        
        const apiUrl = `/${lang}/api/idea/${window.ROOM_CONTEXT.uuid}`;

        const response = await fetch(apiUrl);
        
        const ideas = await response.json();
        renderFeed(ideas);
    } catch (error) {
        console.error("Erro ao navegar nessas águas:", error);
    }
}

function formatRelativeTime(dateString) {
    const past = new Date(dateString.replace(' ', 'T'));
    const now = new Date();
    const diffInSeconds = Math.floor((now - past) / 1000);

    const currentLang = window.VoltI18n.lang || 'en';

    if (diffInSeconds < 60) return window.VoltI18n.translations.now; 
    
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes}${window.VoltI18n.translations.min}`;
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours}${window.VoltI18n.translations.h}`;
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays}${window.VoltI18n.translations.d}`;

    return past.toLocaleDateString(currentLang, { day: '2-digit', month: 'short' }).replace('.', '');
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
    const feedApp = document.getElementById('feed-app');
    if (!ideas || ideas.length === 0) {
        feedApp.innerHTML = `<div class="text-center p-12 text-muted">${window.ROOM_CONTEXT.msg_empty}</div>`;
        return;
    }

    feedApp.innerHTML = ideas.map(idea => {
        return `
        <article class="feed-item bg-main border border-subtle rounded-lg shadow-sm mb-6 volt-animate" data-idea-id="${idea.id}">
            <div class="p-4">
                <header class="flex items-center gap-3 mb-4">
                    <div class="volt-avatar">${idea.author_avatar}</div>
                    <div>
                        <div class="text-main font-bold">${idea.author_name}</div>
                        <div class="text-muted text-sm">${formatRelativeTime(idea.created_at)}</div>
                    </div>
                </header>
                
                <div class="text-main mb-4">${idea.content}</div>

                <div class="flex gap-4 mb-4 border-t border-subtle pt-3">
                    <button class="btn btn-comment-toggle text-accent flex items-center gap-1 text-sm font-semibold" 
                            onclick="toggleCommentForm(${idea.id})">
                        💬 ${window.VoltI18n.translations.comment || 'Comment'}
                    </button>
                </div>

                <div id="comment-form-${idea.id}" class="hidden mb-6 volt-animate bg-subtle p-4 rounded-md">
                    <div class="flex items-center gap-2 mb-3">
                       <span class="text-xs text-muted uppercase font-bold">${window.VoltI18n.translations.avaliation || 'Avaliation'}</span>               
                    <div class="star-rating flex gap-1" data-idea-id="${idea.id}">
                        <span class="star-icon" data-value="5" onclick="setRating(${idea.id}, 5)">★</span>
                        <span class="star-icon" data-value="4" onclick="setRating(${idea.id}, 4)">★</span>
                        <span class="star-icon" data-value="3" onclick="setRating(${idea.id}, 3)">★</span>
                        <span class="star-icon" data-value="2" onclick="setRating(${idea.id}, 2)">★</span>
                        <span class="star-icon" data-value="1" onclick="setRating(${idea.id}, 1)">★</span>
                    </div>
    
                    <input type="hidden" id="rating-input-${idea.id}" value="0">
                    </div>
                    <textarea id="comment-text-${idea.id}" 
                              class="comment-textarea w-full text-sm mb-2" 
                              placeholder="${window.VoltI18n.translations.comment_placeholder || 'Write your review or suggestion...'}"></textarea>
                    <div class="flex justify-end gap-2">
                        <button class="btn text-muted text-xs" onclick="toggleCommentForm(${idea.id})">${window.VoltI18n.translations.cancel || 'Cancel'}</button>
                        <button class="btn btn-primary px-4 py-1 text-xs" onclick="submitComment(${idea.id})">${window.VoltI18n.translations.send || 'Send'}</button>
                    </div>
                </div>
                
                <section class="feed-comments-box rounded-md bg-subtle">
                    ${renderComments(idea.comments)}
                </section>
            </div>
        </article>
        `;
    }).join('');
}

function renderComments(comments) {
    if (!comments || comments.length === 0) {
        return "";
    }

    return comments.map(comment => `
        <div class="feed-comment-item p-2 text-sm flex gap-2">
            <div class="volt-avatar-xs">
                ${comment.avatar ? comment.avatar : comment.author[0]}
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
    `).join('');
}

// Abre e fecha o formulário da ideia específica
function toggleCommentForm(ideaId) {
    const form = document.getElementById(`comment-form-${ideaId}`);
    if (form) {
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            document.getElementById(`comment-text-${ideaId}`).focus();
        }
    }
}

// Função que faremos a seguir para bater no FeedController
// async function submitComment(ideaId) {
//     const textEl = document.getElementById(`comment-text-${ideaId}`);
//     const content = textEl.value.trim();

//     if (content.length < 3) {
//         alert("Comentário muito curto!");
//         return;
//     }

//     console.log(`Enviando comentário para a ideia ${ideaId}: ${content}`);
//     // Próximo passo: o fetch para a API
// }

// function setRating(ideaId, value) {
//     const container = document.querySelector(`.star-rating[data-idea-id="${ideaId}"]`);
//     const stars = container.querySelectorAll('.star-icon');
//     const input = document.getElementById(`rating-input-${ideaId}`);

//     // Atualiza o valor no input escondido
//     input.value = value;

//     // Feedback visual: Pinta as estrelas
//     stars.forEach(star => {
//         const starValue = parseInt(star.getAttribute('data-value'));
//         if (starValue <= value) {
//             star.classList.remove('text-gray-400');
//             star.classList.add('text-yellow-500'); // Cor de destaque
//         } else {
//             star.classList.remove('text-yellow-500');
//             star.classList.add('text-gray-400');
//         }
//     });
// }

function setRating(ideaId, value) {
    const container = document.querySelector(`.star-rating[data-idea-id="${ideaId}"]`);
    const stars = container.querySelectorAll('.star-icon');
    const input = document.getElementById(`rating-input-${ideaId}`);

    input.value = value;

    // Removemos a cor manual via JS e usamos CLASSES, que é mais limpo (Sênior style)
    stars.forEach(star => {
        const starValue = parseInt(star.getAttribute('data-value'));
        
        // Se o valor da estrela for IGUAL ao clicado, marcamos como active
        // O nosso CSS (active ~ star-icon) cuidará de pintar as anteriores
        if (starValue === value) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

async function submitComment(ideaId) {
    const textEl = document.getElementById(`comment-text-${ideaId}`);
    const ratingEl = document.getElementById(`rating-input-${ideaId}`);
    
    const content = textEl.value.trim();
    const rating = parseInt(ratingEl.value);

    // Validação obrigatória
    if (rating === 0) {
        alert("Por favor, selecione uma avaliação de 1 a 5 estrelas! ⭐");
        return;
    }

    if (content.length < 3) {
        alert("O comentário precisa de pelo menos 3 caracteres.");
        return;
    }

    console.log("--- Payload de Comentário ---");
    console.log(`Ideia ID: ${ideaId}`);
    console.log(`Rating: ${rating} estrelas`);
    console.log(`Conteúdo: ${content}`);
    
    // Sucesso inicial: Limpar e fechar
    toggleCommentForm(ideaId);
    textEl.value = '';
    setRating(ideaId, 0); // Reseta as estrelas
}

document.addEventListener('DOMContentLoaded', loadFeed);