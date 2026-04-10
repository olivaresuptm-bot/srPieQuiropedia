document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar");

    
    // 1.  PARA MÓVILES
    const btnAbrirMovil = document.getElementById("btn-menu-movil");
    const btnCerrarMovil = document.getElementById("btn-cerrar-sidebar");

    // Al presionar las rayitas (Hamburguesa), desliza el menú hacia adentro
    if (btnAbrirMovil && sidebar) {
        btnAbrirMovil.addEventListener("click", function() {
            sidebar.classList.add("abierto-movil");
        });
    }

    // Lo vuelve a esconder
    if (btnCerrarMovil && sidebar) {
        btnCerrarMovil.addEventListener("click", function() {
            sidebar.classList.remove("abierto-movil");
        });
    }

    // PARA COMPUTADORAS 
    const btnColapsarEscritorio = document.getElementById('sidebarCollapse');
    
    if (btnColapsarEscritorio && sidebar) {
        btnColapsarEscritorio.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
});