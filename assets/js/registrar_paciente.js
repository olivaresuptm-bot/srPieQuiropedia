function mostrarError(input, mensaje) {
    input.classList.add('is-invalid');
    var t = new bootstrap.Tooltip(input, {
        title: mensaje,
        placement: 'top',
        trigger: 'manual',
        customClass: 'tooltip-error'
    });
    t.show();
}

function quitarError(input) {
    input.classList.remove('is-invalid');
    var instancia = bootstrap.Tooltip.getInstance(input);
    if (instancia) { instancia.dispose(); }
}

var inputFecha = document.getElementsByName('fecha_nac')[0];
if(inputFecha) {
    inputFecha.addEventListener('change', function() {
        var fechaVal = new Date(this.value);
        var hoy = new Date();
        var edad = hoy.getFullYear() - fechaVal.getFullYear();
        var m = hoy.getMonth() - fechaVal.getMonth();

        if (m < 0 || (m === 0 && hoy.getDate() < fechaVal.getDate())) {
            edad--;
        }

        var seccion = document.getElementById('seccion_representante');
        var cRep = document.getElementsByName('cedula_rep')[0];
        var nRep = document.getElementsByName('nombre_rep')[0];
        var pRep = document.getElementsByName('parentesco_rep')[0];

        // AQUÍ ESTÁ LA MAGIA: Menor de 18 o Mayor/Igual a 65 años
        if (edad < 18 || edad >= 65) {
            if(seccion) seccion.style.display = 'block';
            if(cRep) cRep.required = true;
            if(nRep) nRep.required = true;
            if(pRep) pRep.required = true;
        } else {
            if(seccion) seccion.style.display = 'none';
            if(cRep) cRep.required = false;
            if(nRep) nRep.required = false;
            if(pRep) pRep.required = false;
            // Limpiamos los campos por si escribió algo y luego cambió la fecha
            if(cRep) { cRep.value = ""; quitarError(cRep); }
            if(nRep) { nRep.value = ""; quitarError(nRep); }
            if(pRep) { pRep.value = ""; quitarError(pRep); }
        }

        quitarError(this);
        if (fechaVal > hoy) {
            mostrarError(this, "La fecha no puede ser del futuro");
        }
    });
}

document.addEventListener('keypress', function(e) {
    var n = e.target.name;
    var tecla = e.key;

    if (n == 'primer_nombre' || n == 'segundo_nombre' || n == 'primer_apellido' || 
        n == 'segundo_apellido' || n == 'nombre_rep' || n == 'parentesco_rep') {
        var letras = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        if (!letras.test(tecla)) { e.preventDefault(); }
    }

    if (n == 'cedula_id' || n == 'telefono' || n == 'registrado_por' || n == 'cedula_rep') {
        var numeros = /^[0-9+]$/;
        if (!numeros.test(tecla)) { e.preventDefault(); }
    }

    if (n == 'correo' || n == 'instagram') {
        if (tecla == " ") { e.preventDefault(); }
    }
});

document.addEventListener('input', function(e) {
    var el = e.target;
    quitarError(el);

    // Validaciones específicas para el correo
    if (el.name == 'correo') {
        var aviso = document.getElementById('avisoCorreo');
        
        // 1. Bloquear espacios
        if (el.value.indexOf(' ') !== -1) { 
            mostrarError(el, "Sin espacios en el correo"); 
        }
        
        // 2. Mostrar aviso si empezó a escribir y falta el "@"
        if(aviso) {
            if (el.value.length > 0 && el.value.indexOf('@') === -1) {
                aviso.style.display = 'block';
            } else {
                aviso.style.display = 'none';
            }
        }
    }

    // Validaciones para los nombres
    if (el.name == 'primer_nombre' || el.name == 'primer_apellido') {
        if (/[0-9]/.test(el.value)) { mostrarError(el, "No pongas números aquí"); }
    }
});

// --- REVISAR TODO ANTES DE ENVIAR ---
var form = document.querySelector('form');
if(form) {
    form.onsubmit = function(e) {
        var inputs = document.querySelectorAll('input[required]');
        var hayErrores = false;

        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].value == "") {
                hayErrores = true;
                inputs[i].classList.add('is-invalid');
            }
        }

        if (document.querySelectorAll('.is-invalid').length > 0) {
            hayErrores = true;
        }

        if (hayErrores) {
            e.preventDefault();
            // ACTUALIZAMOS EL MENSAJE DE ERROR
            alert("¡Error! Revisa los campos marcados en rojo. Si el paciente es menor de edad o de la tercera edad, los datos del representante son obligatorios.");
        }
    };
}

// --- MAGIA DE LA ALERTA PROGRESIVA (AHORA AFUERA DEL ONSUBMIT) ---
document.addEventListener('DOMContentLoaded', function() {
    const alerta = document.getElementById('alertaObligatorios');
    const inputs = document.querySelectorAll('.form-panel input, .form-panel select, .form-panel textarea');

    if(alerta) {
        inputs.forEach(input => {
            // Cuando el usuario entra a escribir en el campo (focus)
            input.addEventListener('focus', () => {
                alerta.style.display = 'block';
                // Pequeño retraso para que la transición visual (fade in) funcione
                setTimeout(() => alerta.style.opacity = '1', 10);
            });

            // Cuando el usuario sale del campo (blur)
            input.addEventListener('blur', () => {
                // Esperamos unos milisegundos para ver si saltó a otro input
                setTimeout(() => {
                    const elementoActivo = document.activeElement;
                    const esInputFormulario = ['INPUT', 'SELECT', 'TEXTAREA'].includes(elementoActivo.tagName);
                    
                    // Si hizo clic fuera del formulario, ocultamos la alerta
                    if (!esInputFormulario) {
                        alerta.style.opacity = '0';
                        // Esperamos a que termine la animación para desaparecer el cuadro
                        setTimeout(() => alerta.style.display = 'none', 300); 
                    }
                }, 50);
            });
        });
    }
});