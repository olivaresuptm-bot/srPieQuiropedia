let timeoutBusqueda = null;

function filtrarPacientes(event) {
    let input = document.getElementById("inputBusqueda").value.trim();
    let tablaCuerpo = document.getElementById("tablaCuerpo");
    let paginacion = document.querySelector('.card-footer');

    // Manejo de Enter para búsqueda completa (ficha)
    if (event && event.key === 'Enter') {
        event.preventDefault();
        if (typeof realizarBusqueda === 'function') realizarBusqueda();
        return;
    }

    clearTimeout(timeoutBusqueda);

    if (input === "") {
        location.reload();
        return;
    }

    // Esperar 250ms antes de consultar a la BD
    timeoutBusqueda = setTimeout(() => {
        fetch(`gestion_pacientes.php?ajax_filtro=${input}`)
            .then(response => response.text())
            .then(html => {
                tablaCuerpo.innerHTML = html;
                // Aqui se oculta la paginación mientras se filtra para evitar confusión
                if (paginacion) paginacion.style.display = 'none';
            })
            .catch(error => console.error('Error en filtro dinámico:', error));
    }, 250);
}


document.addEventListener('DOMContentLoaded', function() {
            const resultadoFicha = document.getElementById('resultadoFicha');
            const panelOcultable = document.getElementById('panelOcultable');
            const inputBusqueda = document.getElementById('inputBusqueda');

            // Vigila si la ficha de datos de paciente pasa a ser visible
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'style') {
                        if (resultadoFicha.style.display !== 'none') {
                            // Se encontró al paciente, oculta el resto de cosas
                            panelOcultable.style.display = 'none';
                        } else {
                            // No hay ficha, muestra la lista y el registro
                            panelOcultable.style.display = 'block';
                        }
                    }
                });
            });

            if (resultadoFicha) {
                observer.observe(resultadoFicha, { attributes: true });
            }

            // Si el usuario borra lo que escribió en el buscador, limpia la ficha y regresa la vista inicial
            if (inputBusqueda) {
                inputBusqueda.addEventListener('input', function() {
                    if(this.value.trim() === '') {
                        resultadoFicha.style.display = 'none';
                        resultadoFicha.innerHTML = '';
                        panelOcultable.style.display = 'block';
                        filtrarPacientes(); // Restaura la tabla completa
                    }
                });
            }
        });

    // Esto detecta si hay una cédula en la URL y dispara la búsqueda automáticamente
    window.addEventListener('DOMContentLoaded', (event) => {
        const urlParams = new URLSearchParams(window.location.search);
        const busqueda = urlParams.get('busqueda');
        
        if (busqueda) {
            const input = document.getElementById('inputBusqueda');
            input.value = busqueda;
            
            // Llamamos a tu función que ya existe en busqueda_paciente.js
            if (typeof realizarBusqueda === 'function') {
                realizarBusqueda();
            }
        }
    });