document.addEventListener('DOMContentLoaded', function() {
    const services = document.querySelector('#services');
    const total = document.querySelector('#total-value');
    total.textContent = (parseInt(document.querySelector('#cost').value)+ parseInt(services[0].value)).toString();
    services.addEventListener('click', () => {
        total.textContent = (parseInt(document.querySelector('#cost').value)+ parseInt(services.value)).toString();
    });
});