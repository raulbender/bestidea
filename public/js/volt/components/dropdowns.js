const VoltDropdown = {
    init() {
        // Fecha menus se clicar fora da área de gatilho
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.navItem') && !e.target.closest('.wrapper-trigger')) {
                this.closeAll();
            }
        });
    },

    toggle(id, event) {
        if (event) event.stopPropagation();
        
        const menu = document.getElementById(id);
        if (!menu) return;
        
        const isActive = menu.classList.contains('active');
        this.closeAll();
        
        if (!isActive) {
            menu.classList.add('active');
            window.dispatchEvent(new CustomEvent('volt:menuOpened', { detail: { menuId: id } }));
        }
    },

    closeAll() {
        document.querySelectorAll('.drop-down-menu').forEach(m => m.classList.remove('active'));
    }
};