document.addEventListener('DOMContentLoaded', function() {
    function animateNumber(elementId) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const finalNumber = parseInt(element.getAttribute('data-final')) || 0;
        if (finalNumber === 0) {
            element.textContent = "0";
            return;
        }

        let current = 0;
        const increment = Math.ceil(finalNumber / 50);
        const timer = setInterval(function() {
            current += increment;
            if (current >= finalNumber) {
                element.textContent = finalNumber;
                clearInterval(timer);
            } else {
                element.textContent = current;
            }
        }, 20);
    }

    animateNumber('citas_hoy');
    animateNumber('citas_proximas');
    animateNumber('citas_proximos_7dias');
});