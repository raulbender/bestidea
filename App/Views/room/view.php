<link rel="stylesheet" href="/css/volt/modules/feed.css">
<link rel="stylesheet" href="/css/app/bestidea.css">

<div class="feed-sub-navbar">

    <div class="sub-nav-left" style="position: relative;"> <button onclick="VoltShare.handle(event)" class="sub-navbar-button">
            <?php icon('share'); ?>
            <span><?= __('rooms.share') ?></span>
        </button>

        <?php partial('room/share_dropdown'); ?>
    </div>

    <div class="sub-nav-center">
        <button onclick="window.location.reload()" class="sub-navbar-button">
            <?php icon('refresh'); ?>
            <span><?= __('rooms.refresh') ?></span>
        </button>
    </div>


    <div class="sub-nav-right">
        <button onclick="VoltDropdown.toggle('rank-dropdown', event)" class="sub-navbar-button">
            <?php icon('sort'); ?>
            <span><?= __('rooms.sort_by') ?></span>
        </button>

        <?php partial('room/sort_dropdown'); ?>
    </div>

</div>


<div class="content-wrapper">
    <div class="container layout-single">

        <header class="mb-6 p-6 rounded-lg bg-main shadow-sm">
            <div class="flex flex-col gap-2">
                <span class="text-accent font-semibold text-sm uppercase tracking-wider">
                    <?= __('rooms.need_idea_for') ?>
                </span>
                <h1 class="text-2xl font-bold text-main leading-tight">
                    <?= e($data->description) ?>
                </h1>

                <div class="mt-4 flex items-center gap-2 text-muted text-sm">
                    <?php icon('clock', 'w-4 h-4'); ?>
                    <span><?= __('rooms.expires_in') ?>:</span>
                    <span id="countdown" class="font-mono text-primary font-bold" data-expire="<?= $data->expires_at ?>">
                        --:--:--
                    </span>
                </div>
            </div>


            <div class="flex justify-center my-8">
                <button id="btn-show-form" class="btn-primary btn flex items-center gap-2 px-8 py-3 rounded-full shadow-lg">
                    <?php icon('plus', 'w-5 h-5'); ?>
                    <?= __('rooms.contribute') ?>
                </button>
            </div>

            <div id="idea-form-container" class="hidden volt-animate mb-12 mt-12 mx-auto max-w-2xl">
                <div class="bg-main border border-accent p-6 rounded-lg shadow-xl text-center">
                    <h3 class="mb-4 font-bold"><?= __('rooms.share_your_idea') ?></h3>
                    <textarea
                        id="idea-content"
                        class="idea-textarea w-full bg-subtle border-none rounded-md p-4 text-main focus:ring-2 focus:ring-accent"
                        rows="4"
                        placeholder="<?= __('rooms.idea_placeholder') ?>"></textarea>

                    <div class="flex justify-center gap-4 mt-4">
                        <button id="btn-cancel-idea" class="btn text-muted hover:text-main">
                            <?= __('common.cancel') ?>
                        </button>
                        <button id="btn-submit-idea" class="btn btn-primary  btn-accent px-6 py-2">
                            <?= __('rooms.send_idea') ?>
                        </button>
                    </div>
                </div>
            </div>


            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const btnShow = document.getElementById('btn-show-form');
                    const btnCancel = document.getElementById('btn-cancel-idea');
                    const btnSubmit = document.getElementById('btn-submit-idea');
                    const container = document.getElementById('idea-form-container');
                    const textarea = document.getElementById('idea-content');

                    // --- Ação de Toggle (O que faltava) ---
                    const toggleForm = () => {
                        container.classList.toggle('hidden');
                        if (!container.classList.contains('hidden')) {
                            textarea.focus();
                            btnShow.parentElement.classList.add('hidden'); // Esconde o botão "+" ao abrir
                        } else {
                            btnShow.parentElement.classList.remove('hidden'); // Mostra de volta ao cancelar
                        }
                    };

                    btnShow.addEventListener('click', toggleForm);
                    btnCancel.addEventListener('click', () => {
                        textarea.value = '';
                        toggleForm();
                    });

                    // --- Ação de Envio (O Próximo Passo) ---
                    btnSubmit.addEventListener('click', async () => {
                        const content = textarea.value.trim();

                        if (content.length < 5) {
                            VoltAlert.show('Ops!', 'Sua ideia precisa de um pouco mais de substância.', 'error');
                            return;
                        }

                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = 'Enviando...';

                        try {
                            const lang = window.location.pathname.split('/')[1] || window.VoltI18n.lang || 'en';
                            const apiUrl = `/${lang}/api/contribute/${window.ROOM_CONTEXT.uuid}`;
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            const response = await fetch(apiUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    content: content,
                                    room_uuid: window.ROOM_CONTEXT.uuid
                                })
                            });

                            if (response.ok) {
                                textarea.value = '';
                                toggleForm();
                                VoltAlert.show('Sucesso!', 'Sua ideia foi registrada no diário de bordo.', 'success');

                                // Recarrega o feed sem dar F5 na página
                                if (typeof loadFeed === 'function') loadFeed();
                            }
                        } catch (error) {
                            VoltAlert.show('Erro!', 'O mar está agitado. Tente novamente em instantes.', 'error');
                        } finally {
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = "<?= __('rooms.send_idea') ?>";
                        }
                    });
                });
            </script>

        </header>

        <script>
            window.ROOM_CONTEXT = {
                uuid: "<?= $data->uuid ?>",
                msg_empty: "<?= __('rooms.without_ideas') ?>",
                author: {
                    name: "<?= $data->author ?>",
                    avatar: "<?= $data->avatar ?>"
                }
            };
            (function() {
                const auth = window.ROOM_CONTEXT.author;
                if (auth) {
                    const container = document.getElementById('nav-user-context');
                    const nameEl = document.getElementById('nav-username');
                    const avatarEl = document.getElementById('nav-avatar');

                    if (container && nameEl && avatarEl) {
                        nameEl.textContent = auth.name;
                        avatarEl.textContent = auth.avatar;
                        container.classList.remove('hidden'); // Revela a "identidade"
                    }
                }
            })();
        </script>

        <div class="ideas-container">
            <?php partial('../feed/feed'); ?>
        </div>
    </div>
</div>

<script>
    /**
     * Lógica da Contagem Regressiva
     */
    (function() {
        const countdownEl = document.getElementById('countdown');
        const expireDate = new Date(countdownEl.dataset.expire.replace(' ', 'T')).getTime();

        const updateTimer = () => {
            const now = new Date().getTime();
            const distance = expireDate - now;

            if (distance < 0) {
                countdownEl.innerHTML = "<?= __('rooms.expired') ?>";
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownEl.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
        };

        setInterval(updateTimer, 1000);
        updateTimer();
    })();
</script>