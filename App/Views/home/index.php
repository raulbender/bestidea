<div class="home-container">
    <div class="hero-image">
        <?= icon('lightbulb', 'hero-svg') ?>
    </div>
    
    <div class="hero-actions ">
        <a href="<?= route('room_index') ?>" class="btn btn-primary btn-large">
            <?= __('layout.new_idea_button') ?>
        </a>
    </div>
</div>

<style>
    .home-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        /* Usando 80vh para centralizar verticalmente no layout */
        min-height: 80vh; 
        padding: var(--spacing-6);
    }

    .hero-svg svg {
        /* Usando cores do seu core/colors.css se quiser sobrescrever o inline */
        width: 180px;
        height: auto;
        margin-bottom: var(--spacing-8);
        filter: drop-shadow(0 0 20px rgba(244, 187, 44, 0.2));
        transition: transform var(--transition-slow);
    }

    .hero-svg:hover svg {
        transform: scale(1.05) rotate(5deg);
    }

    /* Ajuste para o botão grande usando seus padrões */
    .btn-lg {
        padding: var(--spacing-4) var(--spacing-8);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        letter-spacing: var(--tracking-wider);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
    }
</style>