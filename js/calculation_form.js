const springCleaningType = 'spring-cleaning';
const classicCleaningType = 'classic-cleaning';
const priceType = "PRICE";
const timeType = "TIME";

// Выбрали Генеральную уборку
jQuery('#order-form-cleaning-type-spring-cleaning')
    .on('click', () => {
        // добавить галочки ко всем доп.услугам
        markAllExtrasAs(true);

        updatePricesAndTime();
    });

// Выбрали Классическую уборку
jQuery('#order-form-cleaning-type-classic')
    .on('click', () => {
        // убрать галочки со всех доп.услуг
        markAllExtrasAs(false);

        updatePricesAndTime();
    });

// Если все чекбоксы выбраны - выставить тип Генеральная уборка
jQuery(".order-form-extras-checkbox").on('click', () => {
    if (isAllExtrasSelected()) {
        // выбираем
        jQuery("#order-form-cleaning-type-spring-cleaning").click();

        updatePricesAndTime();
    }
});

function markAllExtrasAs(checked) {
    jQuery('.order-form-extras-checkbox').prop('checked', checked);
}

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

    let cleaningTypeMultiplier = getCleaningTypeMultiplier(priceType, cleaningType);

    return (priceStartingPoint + roomsCost + bathsCost) * cleaningTypeMultiplier;
}

function getCleaningTypeMultiplier(typeOfMultiplier, cleaningType) {
    let resultingMultiplier = 1;

    const springCleaningPriceMultiplier = 2.3;
    const classicCleaningPriceMultiplier = 1;

    const springCleaningTimeMultiplier = 1.5;
    const classicCleaningTimeMultiplier = 1;

    switch (typeOfMultiplier) {
        case priceType:
            if (cleaningType === springCleaningType) {
                resultingMultiplier = springCleaningPriceMultiplier;
            } else if (cleaningType === classicCleaningType) {
                resultingMultiplier = classicCleaningPriceMultiplier
            }
            break;
        case timeType:
            if (cleaningType === springCleaningType) {
                resultingMultiplier = springCleaningTimeMultiplier;
            } else if (cleaningType === classicCleaningType) {
                resultingMultiplier = classicCleaningTimeMultiplier
            }
            break;
        default:
            break;
    }

    return resultingMultiplier;
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
    let discount = .0;
    let priceTagsArray = document.getElementsByClassName('price-tag');
    let length = priceTagsArray.length;

    let currentPrice = recalculatePrice();

    for (let i = 0; i < length; i++) {
        let spanWithCurrency = priceTagsArray[i].innerHTML.split(spanTag)[0] + spanTag;

        // apply discount
        let currentPriceWithDiscount = Math.round(currentPrice - (currentPrice * discount));

        // update value in html
        priceTagsArray[i].innerHTML = spanWithCurrency + currentPriceWithDiscount;

        // update discount for the next option
        if (i === 0) discount += .10;
        else discount += .05;
    }
}

function isAllExtrasSelected() {
    let checkboxes = jQuery('.order-form-extras-checkbox');
    let allCheckBoxes = checkboxes.length;

    for (let checkbox of checkboxes) {
        if (checkbox.checked) allCheckBoxes--;
    }

    if (allCheckBoxes === 0) return true;
}

function recalculateTime() {
    const initialTime = 2;
    const roomsTimeMultiplier = 0.5;
    const bathsTimeMultiplier = 0.5;

    // get amount of rooms selected
    let rooms = getAmountOfRoomsSelected();

    // get amount of baths selected
    let baths = getAmountOfBathsSelected();

    // get type of cleaning selected
    let cleaningType = getTypeOfCleaningSelected();

    // get extras selected
    let extras = getExtrasSelected();

    let roomsTime = rooms * roomsTimeMultiplier;
    let bathsTime = baths * bathsTimeMultiplier;
    let cleaningTypeMultiplier = getCleaningTypeMultiplier(timeType, cleaningType);

    let resultingTime = Math.ceil((initialTime + roomsTime + bathsTime) * cleaningTypeMultiplier);

    if (resultingTime > 8) resultingTime = "8+";

    return resultingTime;
}

function getEndingForNumber(newTime) {
    return newTime === 2 || newTime === 3 || newTime === 4
        ? "часа"
        : "часов";
}

function updateTime() {
    let newTime = recalculateTime();
    let newEnding = getEndingForNumber(newTime);

    document.getElementById("approximate-time-block")
        .getElementsByTagName("h3")
        .item(0)
        .innerText = "Примерное время уборки ~" + newTime + " " + newEnding
}

function restrictNumberValues(input) {
    if (input.value < 1) input.value = 1;
    if (input.value > 1000) input.value = 1000;
}