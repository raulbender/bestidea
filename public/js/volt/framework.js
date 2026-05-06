const VoltFramework = {
    init() {
        // Inicia o observador de cliques fora dos menus
        if (typeof VoltDropdown !== 'undefined') {
            VoltDropdown.init();
        }
        
        console.log("⚡ Volt UI: Motor Modular iniciado.");
    }
};

document.addEventListener('DOMContentLoaded', () => {
    VoltFramework.init();
});