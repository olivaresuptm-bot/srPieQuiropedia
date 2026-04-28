        //El ojo que oculta la clave
        const btn = document.querySelector('#togglePass');
        const input = document.querySelector('#password');
        btn.addEventListener('click', () => {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            btn.querySelector('i').classList.toggle('bi-eye');
            btn.querySelector('i').classList.toggle('bi-eye-slash');
        });
    


        //validacion cedula login, solo numeros

       
    const cedulaInput = document.getElementById('cedula_login');
    const cedulaWarning = document.getElementById('cedula-warning');

    cedulaInput.addEventListener('input', function (e) {
        // Si el usuario teclea algo que no sea un número (0-9)
        if (/[^0-9]/.test(this.value)) {
            cedulaWarning.style.display = 'block'; // Muestra la alerta
            this.value = this.value.replace(/[^0-9]/g, ''); // Borra la letra al instante
            
            // Ocultar alerta después de 2 segundos
            setTimeout(() => { cedulaWarning.style.display = 'none'; }, 2000);
        }
    });
