const VoltAlert = {
    show(title, message, type = 'error', duration = 3000) {
        // Remove alertas anteriores para não empilhar
        document.querySelectorAll('.volt-alert').forEach(el => el.remove());

        const alertDiv = document.createElement('div');
        alertDiv.className = `volt-alert volt-alert-${type}`;

        const icons = { 
            success: '✅', 
            error: '⚠️', 
            info: 'ℹ️', 
            warning: '🔸' 
        };
        const icon = icons[type] || icons.info;

        alertDiv.innerHTML = `
            <div class="alert-icon">${icon}</div>
            <div class="alert-content">
                <strong>${title}</strong>
                <p>${message}</p>
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        `;

        document.body.appendChild(alertDiv);

        // Se duration for 0, o alerta fica fixo até o usuário fechar
        if (duration > 0) {
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.style.opacity = '0';
                    alertDiv.style.transform = 'translateX(50px)';
                    alertDiv.style.transition = '0.5s ease-out';
                    setTimeout(() => alertDiv.remove(), 500);
                }
            }, duration);
        }
    }
};