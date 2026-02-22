    const btn = document.querySelector('#togglePass');
    const input = document.querySelector('#nueva_clave');

    btn.addEventListener('click', () => {
        // Comparamos contra 'password', no contra el nombre del campo
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        // Cambiamos el icono
        const icon = btn.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
