const FREQUENCY = {
    ONCE: "once",
    MONTHLY: "monthly",
    TWOWEEKLY: "twoweekly",
    OFTEN: "often"
};

jQuery("a[href='#order-frequency-option-once']").on("click", () => {
    processClickOnOrderButton(FREQUENCY.ONCE);
});

jQuery("a[href='#order-frequency-option-monthly']").on("click", () => {
    processClickOnOrderButton(FREQUENCY.MONTHLY);
});

jQuery("a[href='#order-frequency-option-twoweekly']").on("click", () => {
    processClickOnOrderButton(FREQUENCY.TWOWEEKLY);
});

jQuery("a[href='#order-frequency-option-often']").on("click", () => {
    processClickOnOrderButton(FREQUENCY.OFTEN);
});


function processClickOnOrderButton(frequency) {
    let orderData = collectOrderData();

    let result = sendOrderDataOnBackend(orderData, frequency);

    showResultToUser(result);
}

function collectOrderData() {
    function getValueForInput(id) {
        return document.getElementById(id).value;
    }

    return {
        rooms: getAmountOfRoomsSelected(),
        baths: getAmountOfBathsSelected(),
        cleaning_type: getTypeOfCleaningSelected(),
        selected_extras: getExtrasSelected(),
        customer: {
            name: getValueForInput("order-form-name"),
            date: getValueForInput("order-form-datetime"),
            phone: getValueForInput("order-form-phone"),
            address: getValueForInput("order-form-address"),
            email: getValueForInput("order-form-email"),
        }
    };
}

function sendOrderDataOnBackend(orderData, frequency) {
    let requestBody = JSON.stringify({orderData: orderData, frequency: frequency});
    let url = 'http://xn--90aia2asp.xn--90ais/wp-admin/admin-ajax.php';
    let action = "order";

    let fetchInput = `${url}?action=${action}&body=${encodeURI(requestBody)}`;

    let response = fetch(fetchInput, {
        method: "POST",
    });

    return response.text();
}

function showResultToUser(result) {

}

