<?php

// function that works with sent order data
function order_function()
{
    $body = getBodyAsObject();

    // Create connection
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Save customer to DB
    $customerId = saveCustomerToDB($body->customer, $connection);

    // Save order to DB
    $result = saveOrderToDB($body, $customerId, $connection);

    // Show results of saving on frontend
    echo $result;

    // Close the connection
    $connection->close();

    // action finished
    die;
}


/**
 * Conversion from query parameter to Object
 * @return mixed
 */
function getBodyAsObject()
{
    //{
    //   "rooms":"3",
    //   "baths":"1",
    //   "cleaningType":"classic-cleaning",
    //   "selectedExtras":[
    //      "order-form-extras-windows",
    //      "order-form-extras-optimisation",
    //      "order-form-extras-kitchen-inside-technique",
    //      "order-form-extras-balcony"
    //   ],
    //   "date":"2019-12-20T12:44",
    //   "customer":{
    //      "name":"Имя Фамилия",
    //      "phone":"80256151788",
    //      "address":"Минск Космонавтов 47-24",
    //      "email":"kontrol@mail.ru"
    //   },
    //   "approximateCost":66,
    //   "approximateTime":4,
    //   "frequency":"monthly"
    //}
    return json_decode(urldecode(explode("body=", $_SERVER['QUERY_STRING'])[1]));
}

/**
 * @param stdClass $customer
 * @param mysqli $connection
 * @return int
 */
function saveCustomerToDB(stdClass $customer, mysqli $connection)
{
    $customerId = 0;

    // check if such customer exists -> get its id

    // save to db if not exists and get its id

    return $customerId;
}

/**
 * Save order to DB
 * @param stdClass $body
 * @param $customerId
 * @param mysqli $connection
 * @return string
 */
function saveOrderToDB(stdClass $body, $customerId, mysqli $connection)
{
    $table = "order_data";

    $extrasAsReadableText = implode(", ", extrasToReadableArray($body->selectedExtras));

    // build insert statement
    $columns = "rooms, baths, cleaning_type, selected_extras, customer_id, date,
                   frequency, approximate_cost, approximate_time";

    $values = "$body->rooms, $body->baths, $body->cleaningType, $extrasAsReadableText, $customerId, $body->date,
               $body->frequency, $body->approximate_cost, $body->approximate_time";

    $sql = "INSERT INTO $table ($columns) VALUES ($values)";

//     insert order data into db
//    if ($connection->query($sql) === TRUE) {
//        return "Success!";
//    } else {
//        return "Error: $sql <br> $connection->error";
//    }

}

/**
 * Convert extras from bare id array to readable array of values
 * @param array $extrasArray
 * @return array
 */
function extrasToReadableArray(array $extrasArray)
{
    $result = array();
    $prefix = "order-form-extras-";

    foreach ($extrasArray as $item) {
        $value = "";

        if ($item === $prefix . "windows") $value = "Мойка Окон";
        if ($item === $prefix . "optimisation") $value = "Оптимизация внутреннего пространства шкафов";
        if ($item === $prefix . "kitchen-inside-technique") $value = "Мойка кухонной техники изнутри";
        if ($item === $prefix . "kitchen-inside-cabinets") $value = "Мойка шкафчиков изнутри";
        if ($item === $prefix . "kitchen-inside-cabinets") $value = "Мойка шкафчиков изнутри";
        if ($item === $prefix . "balcony") $value = "Уборка балкона";
        if ($item === $prefix . "ironing") $value = "Глажка вещей";
        if ($item === $prefix . "fridge") $value = " Чистка холодильника и морозильной камеры";

        $result[] = $value;
    }

    return $result;
}

?>