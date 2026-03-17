
        function verificarMetodo() {
            var metodo = document.getElementById('metodo_pago').value;
            var cajaRef = document.getElementById('caja_referencia');
            var inputRef = document.getElementById('referencia');
            
            if (metodo === 'pago_movil' || metodo === 'transferencia' || metodo === 'punto') {
                cajaRef.style.display = 'block';
                inputRef.setAttribute('required', 'required');
            } else {
                cajaRef.style.display = 'none';
                inputRef.removeAttribute('required');
                inputRef.value = '';
            }
        }
