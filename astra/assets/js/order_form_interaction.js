const springCleaningType = 'spring-cleaning';
const classicCleaningType = 'classic-cleaning';
const priceType = "PRICE";
const timeType = "TIME";

const MULTIPLIER = 3;

const PRICES = {
    ROOM: 14,
    BATH: 15,
    START: 16,
    VACUUM_CLEANER: 5,
    EXTRAS: {
        WINDOW: 8,
        FRIDGE: 12,
        MICROWAVE_OVEN: 8,
        OVEN: 15,
        COOKER_HOOD: 15,
        DISHES: 10,
        KEYS: 10,
        BALCONY: 12,
        CABINETS: 18,
        IRONING: 10,
        OPTIMISATION: 10
    }
};

let currentPrices = {
    once: 0,
    monthly: 0,
    twoweekly: 0,
    weekly: 0
};

let currentTimeHours = 0;

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
        jQuery("#order-form-cleaning-type-spring-cleaning").click();
    }
    updatePricesAndTime();
});

function markAllExtrasAs(checked) {
    let checkboxes = jQuery('.order-form-extras-checkbox');
    checkboxes.prop('checked', checked);
    for (let checkbox of checkboxes) {
        if (checkbox.value === "windows")
            handleClickOnWindowsCheck(checkbox)
    }
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

function handleVacuumCleanerClick(checkbox) {
    updatePricesAndTime();
}

((() => {
    // update prices with the very beginning
    updatePricesAndTime();

    jQuery("<br>").insertAfter(".price-tag")

})());

function updatePricesAndTime() {
    updatePrices();
    updateTime();
}

function hasVacuumCleaner() {
    let checkbox = jQuery('#order-form-vacuum-cleaner')[0];
    return checkbox.checked ? "true" : "false";
}

function handleClickOnWindowsCheck(checkbox) {
    let visible = "windows-input-visible";
    let invisible = "windows-input-invisible";

    let input = document.getElementById("order-form-extras-windows-amount");
    let text = document.getElementById("order-form-extras-windows-amount-text");

    if (checkbox.checked) {
        input.classList.replace(invisible, visible);
        text.classList.replace(invisible, visible);
        text.style.marginBottom = "auto";
        input.value = 1;
    } else {
        input.classList.replace(visible, invisible);
        text.classList.replace(visible, invisible);
        input.value = 0;
    }
}

function handleWindowsAmountChange(input) {
    restrictNumberValues(input);
    updatePricesAndTime();
}

function getWindowsAmount() {
    let input = document.getElementById("order-form-extras-windows-amount");

    return input.value;
}

function getSumOfExtras(extras) {
    return Object.entries(extras).reduce((sum, [option, value]) => {
        if (value !== 0) {
            switch (option) {
                case "windows":
                    return sum += PRICES.EXTRAS.WINDOW * value;
                case "fridge":
                    return sum += PRICES.EXTRAS.FRIDGE;
                case "microwave-oven":
                    return sum += PRICES.EXTRAS.MICROWAVE_OVEN;
                case "oven":
                    return sum += PRICES.EXTRAS.OVEN;
                case "cooker-hood":
                    return sum += PRICES.EXTRAS.COOKER_HOOD;
                case "kic":
                    return sum += PRICES.EXTRAS.CABINETS;
                case "dishes":
                    return sum += PRICES.EXTRAS.DISHES;
                case "balcony":
                    return sum += PRICES.EXTRAS.BALCONY;
                case "ironing":
                    return sum += PRICES.EXTRAS.IRONING;
                case "optimisation":
                    return sum += PRICES.EXTRAS.OPTIMISATION;
            }
        }
        return sum;
    }, 0);
}

function recalculatePrice() {
    // calculate resulting price for selected items
    let basicPrice = PRICES.START
        + (PRICES.ROOM * getAmountOfRoomsSelected())
        + (PRICES.BATH * getAmountOfBathsSelected());

    // if there is no vacuum cleaner - add its cost per order
    if (hasVacuumCleaner() === "false") basicPrice += PRICES.VACUUM_CLEANER;

    // get extras selected
    let extrasMap = getExtrasSelected();

    return (basicPrice + getSumOfExtras(extrasMap)) * MULTIPLIER;
    // return basicPrice + getSumOfExtras(extrasMap);
}

function getCleaningTypeMultiplier(typeOfMultiplier, cleaningType) {
    let resultingMultiplier = 1;

    const springCleaningPriceMultiplier = 2;
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
    let checkboxes = jQuery('.order-form-extras-checkbox');
    let selectedExtras = {};

    for (let checkbox of checkboxes) {
        let amount = 0;
        if (checkbox.checked) {
            if (checkbox.value === "windows") amount = getWindowsAmount();
            else amount = 1;

            selectedExtras[checkbox.value] = amount;
        }
    }

    return selectedExtras;
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

function updateGlobalPrice(price, priceNumber) {
    switch (priceNumber) {
        case 0:
            currentPrices.once = price;
            break;
        case 1:
            currentPrices.monthly = price;
            break;
        case 2:
            currentPrices.twoweekly = price;
            break;
        case 3:
            currentPrices.weekly = price;
            break;
    }
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

        // update value in global variables
        updateGlobalPrice(currentPriceWithDiscount, i);

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

function getApproximateCost(frequency) {
    let cost = 0;

    switch (frequency) {
        case FREQUENCY.ONCE:
            cost = currentPrices.once;
            break;
        case FREQUENCY.MONTHLY:
            cost = currentPrices.monthly;
            break;
        case FREQUENCY.TWOWEEKLY:
            cost = currentPrices.twoweekly;
            break;
        case FREQUENCY.WEEKLY:
            cost = currentPrices.weekly;
            break;
    }

    return cost;
}

function getApproximateTime() {
    return currentTimeHours;
}

function updateTime() {
    let newTime = recalculateTime();
    let newEnding = getEndingForNumber(newTime);

    // update global variable
    currentTimeHours = newTime;

    document.getElementById("approximate-time-block")
        .getElementsByTagName("h3")
        .item(0)
        .innerText = "Примерное время уборки ~" + newTime + " " + newEnding
}

function restrictNumberValues(input) {
    if (input.value < 1) input.value = 1;
    if (input.value > 1000) input.value = 1000;
}