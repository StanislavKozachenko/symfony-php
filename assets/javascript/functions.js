document.addEventListener('DOMContentLoaded', function() {
    const services = document.querySelector('#services');
    const total = document.querySelector('#total-value');
    total.textContent = (parseInt(document.querySelector('#cost').value)+ parseInt(services[0].value)).toString();
    services.addEventListener('click', () => {
        let sum = 0;
        for(let option of services.options){
            if(option.selected){
                sum+= parseInt(option.value);
            }
        }
        total.textContent = (parseInt(document.querySelector('#cost').value)+ parseInt(sum)).toString();
    });
});