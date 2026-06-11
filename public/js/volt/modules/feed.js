
async function loadFeed() {
    try {
        const lang = window.location.pathname.split('/')[1] || window.VoltI18n.lang || 'en';
        const apiUrl = `/${lang}/api/idea/${window.ROOM_CONTEXT.uuid}`;

        const response = await fetch(apiUrl);
        const ideas = await response.json();
        
        // 1. Renderiza os cards puros do servidor
        renderFeed(ideas);
        
        // 2. MAGIA: Aplica a ordenação salva no LocalStorage silenciosamente
        if (typeof VoltFeedSort !== 'undefined') {
            VoltFeedSort.applyCurrent();
        }
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

        // --- CÁLCULO DE ORDENAÇÃO ---
        const createdTime = new Date(idea.created_at.replace(' ', 'T')).getTime();
        let lastCommentTime = 0; 
        if (idea.comments && idea.comments.length > 0) {
            const commentTimes = idea.comments.map(c => new Date(c.created_at.replace(' ', 'T')).getTime());
            lastCommentTime = Math.max(...commentTimes);
        }
        const votes = parseFloat(idea.average_rating) || 0;
        // -----------------------------

        return `
        <article class="feed-item bg-main border border-subtle rounded-lg shadow-sm mb-6 volt-animate" 
                data-idea-id="${idea.id}" 
                data-created="${createdTime}"
                data-comment="${lastCommentTime}"
                data-votes="${votes}">
            <div class="p-4">
                    <header class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="volt-avatar">${idea.author_avatar}</div>
                            
                            <div class="flex flex-col">
                                <div class="text-main font-bold text-sm leading-tight">
                                    ${idea.author_name}
                                </div>
                                <div class="text-muted text-sm mt-0.5">
                                    ${formatRelativeTime(idea.created_at)}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 bg-subtle px-2 py-1 rounded-md border border-subtle">
                            <span class="star-primary text-xs">★</span>
                            <span class="text-main font-bold text-xs">
                                ${idea.average_rating > 0 ? idea.average_rating : '--'}
                            </span>
                            <span class="text-muted text-[10px]">(${idea.total_comments})</span>
                        </div>
                    </header>
                
                <div class="text-main mb-4">${idea.content}</div>

                <div class="flex gap-4 mb-4 border-t border-subtle pt-3">
                    <button id="btn-comment-toggle-${idea.id}" class="btn btn-comment-toggle text-accent flex items-center gap-1 text-sm font-semibold" 
                            onclick="toggleCommentForm(${idea.id})">
                        ⭐ ${window.VoltI18n.translations.evaluate}
                    </button>
                </div>

                <div id="comment-form-${idea.id}" class="hidden mb-6 volt-animate bg-subtle p-4 rounded-md">
                    <div class="flex items-center gap-2 mb-3">
                       <span class="text-xs text-muted uppercase font-bold">${window.VoltI18n.translations.evaluation}</span>               
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
                              placeholder="${window.VoltI18n.translations.comment_placeholder}"></textarea>
                    <div class="flex justify-end gap-2">
                        <button class="btn text-muted text-xs" onclick="toggleCommentForm(${idea.id})">${window.VoltI18n.translations.cancel}</button>
                        <button id="btn-submit-${idea.id}" class="btn btn-primary px-4 py-1 text-xs" onclick="submitComment(${idea.id})">${window.VoltI18n.translations.send}</button>
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


function toggleCommentForm(ideaId) {
    const form = document.getElementById(`comment-form-${ideaId}`);
    const btnToggle = document.getElementById(`btn-comment-toggle-${ideaId}`);
    
    if (form && btnToggle) {
        const isHidden = form.classList.contains('hidden');
        
        if (isHidden) {
            form.classList.remove('hidden');
            btnToggle.classList.add('hidden'); // Some com o botão ao abrir
            document.getElementById(`comment-text-${ideaId}`).focus();
        } else {
            form.classList.add('hidden');
            btnToggle.classList.remove('hidden'); // Reaparece ao cancelar/fechar
        }
    }
}

function setRating(ideaId, value) {
    const container = document.querySelector(`.star-rating[data-idea-id="${ideaId}"]`);
    const stars = container.querySelectorAll('.star-icon');
    const input = document.getElementById(`rating-input-${ideaId}`);

    input.value = value;

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
    const btnSubmit = document.getElementById(`btn-submit-${ideaId}`);
    
    const content = textEl.value.trim();
    const rating = parseInt(ratingEl.value);

    if (rating === 0) {
        VoltAlert.show('Atenção', "Por favor, selecione uma avaliação! ⭐", 'warning');
        return;
    }
    if (content.length < 3) {
        VoltAlert.show('Atenção', "O comentário é muito curto.", 'warning');
        return;
    }

    const originalText = btnSubmit.innerHTML;
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '...';

    try {
        const lang = window.location.pathname.split('/')[1] || window.VoltI18n.lang || 'en';
        const apiUrl = `/${lang}/api/comment/${window.ROOM_CONTEXT.uuid}/${ideaId}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken 
            },
            body: JSON.stringify({
                content: content,
                rating: rating
            })
        });

        const result = await response.json();

        if (response.ok) {
            VoltAlert.show('Sucesso', "Comentário enviado!", 'success');
            
            textEl.value = '';
            setRating(ideaId, 0);
            toggleCommentForm(ideaId);
            
            loadFeed(); 
        } else {
            throw new Error(result.error || 'Erro ao comentar');
        }

    } catch (error) {
        VoltAlert.show('Erro', error.message, 'danger');
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalText;
    }
}


/**
 * Motor de Ordenação Client-Side do Volt R²
 */
const VoltFeedSort = {
    // Cria uma chave única para cada sala (ex: volt_sort_abc123)
    getStorageKey() {
        return `volt_sort_pref_${window.ROOM_CONTEXT.uuid}`;
    },

    // Lê a preferência do navegador (se não existir, default é 'recent')
    getCurrentCriteria() {
        return localStorage.getItem(this.getStorageKey()) || 'recent';
    },

    // Aplica a configuração atual (usado no loadFeed)
    applyCurrent() {
        this.apply(this.getCurrentCriteria(), false);
    },

    // O maestro que executa a ordenação
    apply(criteria, saveChoice = true) {
        const container = document.getElementById('feed-app');
        if (!container) return;

        const cards = Array.from(container.querySelectorAll('.feed-item'));
        if (cards.length === 0) return;

        // Executa a ordenação matemática
        if (criteria === 'recent') this.sortByRecent(cards);
        if (criteria === 'comment') this.sortByLastComment(cards);
        if (criteria === 'votes') this.sortByVotes(cards);

        // Reoxigena o DOM
        cards.forEach(card => container.appendChild(card));

        // Se foi um clique do usuário (saveChoice = true), grava no LocalStorage
        if (saveChoice) {
            localStorage.setItem(this.getStorageKey(), criteria);
        }

        // Fecha o menu após a escolha
        if (typeof VoltDropdown !== 'undefined') VoltDropdown.closeAll();
    },

    sortByRecent(cards) {
        cards.sort((a, b) => parseInt(b.dataset.created) - parseInt(a.dataset.created));
    },

    // 💬 Especialista 2: Últimos Comentados (Blindado contra bugs de vazios)
    sortByLastComment(cards) {
        cards.sort((a, b) => {
            const timeA = parseInt(a.dataset.comment);
            const timeB = parseInt(b.dataset.comment);

            // Caso 1: Ambas têm comentários -> Ordena pelo comentário mais recente
            if (timeA > 0 && timeB > 0) {
                return timeB - timeA;
            }
            
            // Caso 2: Apenas a ideia 'b' tem comentários -> 'b' fica em primeiro
            if (timeB > 0 && timeA === 0) return 1;
            
            // Caso 3: Apenas a ideia 'a' tem comentários -> 'a' fica em primeiro
            if (timeA > 0 && timeB === 0) return -1;

            // Caso 4: Nenhuma das duas tem comentários -> Desempata pela ideia mais recente criada
            return parseInt(b.dataset.created) - parseInt(a.dataset.created);
        });
    },

    sortByVotes(cards) {
        cards.sort((a, b) => {
            const voteDiff = parseFloat(b.dataset.votes) - parseFloat(a.dataset.votes);
            if (voteDiff === 0) return parseInt(b.dataset.created) - parseInt(a.dataset.created);
            return voteDiff;
        });
    }
};



document.addEventListener('DOMContentLoaded', loadFeed);