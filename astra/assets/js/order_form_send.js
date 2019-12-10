const FREQUENCY = {
    ONCE: "once",
    MONTHLY: "monthly",
    TWOWEEKLY: "twoweekly",
    WEEKLY: "weekly"
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
    processClickOnOrderButton(FREQUENCY.WEEKLY);
});


function processClickOnOrderButton(frequency) {
    let orderData = collectOrderData(frequency);

    let result = sendOrderDataOnBackend(orderData);

    showResultToUser(result);
}

function collectOrderData(frequency) {
    function getValueForInput(id) {
        return document.getElementById(id).value;
    }

    return {
        rooms: getAmountOfRoomsSelected(),
        baths: getAmountOfBathsSelected(),
        cleaningType: getTypeOfCleaningSelected(),
        selectedExtras: getExtrasSelected(),
        date: getValueForInput("order-form-datetime"),
        customer: {
            name: getValueForInput("order-form-name"),
            phone: getValueForInput("order-form-phone"),
            address: getValueForInput("order-form-address"),
            email: getValueForInput("order-form-email"),
        },
        approximateCost: getApproximateCost(frequency),
        approximateTime: getApproximateTime(),
        frequency: frequency,
        hasVacuumCleaner: hasVacuumCleaner()
    };
}

function sendOrderDataOnBackend(orderData) {
    let requestBody = JSON.stringify(orderData);
    let url = 'http://xn--90aia2asp.xn--90ais/wp-admin/admin-ajax.php';
    let action = "order";

    let fetchInput = `${url}?action=${action}&body=${requestBody}`;

    let response = fetch(fetchInput, {
        method: "POST",
    });

    return response.json;
}

function showResultToUser(result) {

}

