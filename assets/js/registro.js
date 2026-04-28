
        document.addEventListener("DOMContentLoaded", function() {
            
            // 1. Lógica para los campos obligatorios
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            
            requiredFields.forEach(field => {
                // Creamos un mensajito oculto debajo de cada campo obligatorio
                const aviso = document.createElement('div');
                aviso.className = 'text-danger small mt-1';
                aviso.style.display = 'none';
                aviso.innerHTML = '<i class="bi bi-exclamation-circle"></i> Este dato es obligatorio';
                
                // Si es un input-group (como la clave), lo ponemos debajo de todo el grupo
                if (field.parentNode.classList.contains('input-group')) {
                    field.parentNode.parentNode.appendChild(aviso);
                } else {
                    field.parentNode.appendChild(aviso);
                }

                // Al hacer clic en la casilla
                field.addEventListener('focus', () => {
                    if (field.value.trim() === '') {
                        aviso.style.display = 'block';
                    }
                });

                // Mientras escribe, lo ocultamos
                field.addEventListener('input', () => {
                    if (field.value.trim() !== '') {
                        aviso.style.display = 'none';
                    } else {
                        aviso.style.display = 'block';
                    }
                });

                // Al quitar el ratón de la casilla, lo escondemos para no ensuciar la pantalla
                field.addEventListener('blur', () => {
                    aviso.style.display = 'none';
                });
            });

            // 2. Lógica especial para el campo de Correo (@)
            const correoField = document.querySelector('input[name="correo"]');
            const avisoCorreo = document.createElement('div');
            avisoCorreo.className = 'text-danger small mt-1 fw-bold';
            avisoCorreo.style.display = 'none';
            avisoCorreo.innerHTML = 'Se requiere un "@" para que sea válido';
            correoField.parentNode.appendChild(avisoCorreo);

            correoField.addEventListener('input', () => {
                // Si ya empezó a escribir algo y no ha puesto el @
                if (correoField.value.length > 0 && !correoField.value.includes('@')) {
                    avisoCorreo.style.display = 'block';
                } else {
                    avisoCorreo.style.display = 'none';
                }
            });
        });
