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

jQuery("a[href='#order-frequency-option-weekly']").on("click", () => {
    processClickOnOrderButton(FREQUENCY.WEEKLY);
});


function validateOrderData(orderData) {
    let errors = [];

    // if (orderData.customer.name === "") errors.push("Имя должно быть заполнено.");
    // if (orderData.customer.address === "") errors.push("Адрес должен быть заполнен");
    if (orderData.customer.phone === "" && orderData.customer.email === "") {
        errors.push("В заказе должен присутствовать телефон, или email");
    }
    // if (orderData.date === "") errors.push("Дата и время заказа не выбрано");
    // if (orderData.cleaningType === "") errors.push("Вид уборки не выбран (Классическая, или Генеральная)");

    return errors;
}

function processClickOnOrderButton(frequency) {
    let orderData = collectOrderData(frequency);

    let errors = validateOrderData(orderData);

    if (errors.length === 0) {
        sendOrderDataOnBackend(orderData);
    } else {
        alert(errors.map(error => "- " + error).join("\n"));
    }
}

function collectOrderData(frequency) {
    function getValueForInput(id) {
        return document.getElementById(id).value;
    }

    return {
        rooms: getAmountOfRoomsSelected(),
        baths: getAmountOfBathsSelected() + getAmountOfKitchensSelected(), // TODO костыль, из-за того что ванные комнаты и кухни одинаковы по стоимости
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
    }).then(r => r.text())
        .then(r => showResultToUser(r));
}

function showResultToUser(result) {
    alert(result);
}

