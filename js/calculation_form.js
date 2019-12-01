const springCleaningType = 'spring-cleaning';
const classicCleaningType = 'classic-cleaning';

// Выбрали Генеральную уборку
jQuery('#order-form-cleaning-type-spring-cleaning')
    .on('click', () => {
        // добавить галочки ко всем доп.услугам
        jQuery('.order-form-extras-checkbox').prop('checked', true);

        updatePricesAndTime();
    });

// Выбрали Классическую уборку
jQuery('#order-form-cleaning-type-classic')
    .on('click', () => {
        // убрать галочки со всех доп.услуг
        jQuery('.order-form-extras-checkbox').prop('checked', false);

        updatePricesAndTime();
    });

function handleNumberInputChange(input) {
    restrictNumberValues(input);
    updatePricesAndTime();
}

function handleCalculateButtonClick(button) {
    // synchronize rooms input values
    let calculationFormRoomsValue = document.getElementById("calculation-form-rooms").value;
    let orderFormRooms = document.getElementById("order-form-rooms");

    orderFormRooms.value = calculationFormRoomsValue;

    // synchronize baths input values
    let calculationFormBathsValue = document.getElementById("calculation-form-baths").value;
    let orderFormBaths = document.getElementById("order-form-baths");

    orderFormBaths.value = calculationFormBathsValue;

    // update prices and time
    updatePricesAndTime()
}

// update prices with the very beginning
((() => {
    updatePricesAndTime();
})());

function updatePricesAndTime() {
    updatePrices();
    updateTime();
}

function recalculatePrice() {
    const pricePerRoom = 14;
    const pricePerBath = 15;
    const priceStartingPoint = 16;
    const springCleaningMultiplier = 2;
    const classicCleaningMultiplier = 1;

    // get amount of rooms selected
    let rooms = getAmountOfRoomsSelected();

    // get amount of baths selected
    let baths = getAmountOfBathsSelected();

    // get type of cleaning selected
    let cleaningType = getTypeOfCleaningSelected();

    // get extras selected
    let extras = getExtrasSelected();

    // calculate resulting price for selected items
    let roomsCost = pricePerRoom * rooms;

    let bathsCost = pricePerBath * baths;

    let cleaningTypeMultiplier = cleaningType === springCleaningType
        ? springCleaningMultiplier
        : classicCleaningMultiplier;

    return (priceStartingPoint + roomsCost + bathsCost) * cleaningTypeMultiplier;
}

function getExtrasSelected() {
    // TODO implement in further versions
    return undefined;
}

function getAmountOfRoomsSelected() {
    return document.getElementById('order-form-rooms').value;
}

function getTypeOfCleaningSelected() {
    if (document.getElementById('order-form-cleaning-type-spring-cleaning').checked) {
        return springCleaningType
    } else if (document.getElementById('order-form-cleaning-type-classic').checked) {
        return classicCleaningType
    } else {
        return classicCleaningType;
    }

}

function getAmountOfBathsSelected() {
    return document.getElementById('order-form-baths').value;
}

function updatePrices() {
    let spanTag = '</span>';
    let discount = 0;
    let priceTagsArray = document.getElementsByClassName('price-tag');
    let length = priceTagsArray.length;

    let currentPrice = recalculatePrice();

    for (let i = 0; i < length; i++) {
        let spanWithCurrency = priceTagsArray[i].innerHTML.split(spanTag)[0] + spanTag;

        // apply discount
        let currentPriceWithDiscount = currentPrice - (currentPrice * discount);

        // update value in html
        priceTagsArray[i].innerHTML = spanWithCurrency + currentPriceWithDiscount;

        // update discount for the next option
        if (i === 0) discount += 10;
        else discount += 5;
    }
}

function updateTime() {
    // TODO implement
}

function restrictNumberValues(input) {
    if (input.value < 1) input.value = 1;
    if (input.value > 1000) input.value = 1000;
}