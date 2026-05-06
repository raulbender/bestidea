const VoltTheme = {
    toggle() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('volt_theme', newTheme);
        
        this.updateButton(newTheme);        
        
        if (typeof VoltAlert !== 'undefined') {
            VoltAlert.show('Tema Alterado', `O tema foi alterado para ${newTheme}.`, 'success');
        }
        
        window.dispatchEvent(new CustomEvent('volt:themeChanged', { detail: { theme: newTheme } }));
    },

    updateButton(theme) {
        const btn = document.getElementById('themeToggle');
        if (!btn) return;
        btn.innerHTML = (theme === 'dark') ? window.FrameworkConfig.i18n.themeLight : window.FrameworkConfig.i18n.themeDark;
    }
};