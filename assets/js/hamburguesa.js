document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar");
    
    // ==========================================
    // 1. LÓGICA PARA MÓVILES (Menú lateral)
    // ==========================================
    const btnAbrirMovil = document.getElementById("btn-menu-movil");
    const btnCerrarMovil = document.getElementById("btn-cerrar-sidebar");

    // Abrir menú con la hamburguesa
    if (btnAbrirMovil && sidebar) {
        btnAbrirMovil.addEventListener("click", function(evento) {
            evento.stopPropagation(); // VITAL: Evita que el clic se pase al documento y lo cierre de inmediato
            sidebar.classList.add("abierto-movil");
        });
    }

    // Cerrar menú con el botón de la X (si lo tienes)
    if (btnCerrarMovil && sidebar) {
        btnCerrarMovil.addEventListener("click", function() {
            sidebar.classList.remove("abierto-movil");
        });
    }

    // NUEVA MAGIA: Cerrar al tocar en cualquier parte de la pantalla oscura
    document.addEventListener("click", function(evento) {
        // Solo verificamos si estamos en celular (menor a 768px) y si el menú está abierto
        if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains("abierto-movil")) {
            
            // Si el toque NO fue dentro del menú azul...
            if (!sidebar.contains(evento.target)) {
                sidebar.classList.remove("abierto-movil");
            }
        }
    });

    // ==========================================
    // 2. LÓGICA PARA COMPUTADORAS (Colapsar)
    // ==========================================
    const btnColapsarEscritorio = document.getElementById('sidebarCollapse');
    
    if (btnColapsarEscritorio && sidebar) {
        btnColapsarEscritorio.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
});