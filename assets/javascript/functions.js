document.addEventListener('DOMContentLoaded', function() {
    const change = document.querySelector('.rates-changer');
     function changeValue(count, value){
        let services = document.querySelectorAll('.service-item');
        if(value !== "BYN"){
            for (let service of services) {
                service.textContent = service.value.split('-')[1] + ' - ' + (service.value.split('-')[0] / count).toFixed(2) + " " + value;
            }
        } else {
            for (let service of services) {
                service.textContent = service.value.split('-')[1] + ' - ' + service.value.split('-')[0] + " " + value;
            }
        }
    }
    function changeTotal(){
        const total = document.querySelector('#total-value');
        const services = document.querySelector('#services');
        let sum = parseFloat(document.querySelector("#rates").value.split(' ')[0]);
        let exchange = document.querySelector('.rates-changer').value.split('-')[2];
        for(let service of services.options){
            if(service.selected){
                sum+=parseFloat(service.textContent.split('-')[1].split(' ')[1]);
            }
        }
        sum = sum.toFixed(2);
        total.textContent = sum + " " + exchange.toString().toUpperCase();
    }

    let services = document.querySelector('#services');
    services.addEventListener('click', () => {
        changeTotal();
    });

    change.addEventListener('click', () => {
        changeTotal();
        let count = change.value.split('-')[1];
        let rate = change.value.split('-')[2];
        switch (rate) {
            case "byn":
                changeValue(count, "BYN");
                break;
            case "usd":
                console.log('usd');
                changeValue(count, "USD");
                break;
            case "eur":
                changeValue(count, "EUR");
                break;
            case "rub":
                changeValue(count, "RUB");
                break;
        }
    });
});