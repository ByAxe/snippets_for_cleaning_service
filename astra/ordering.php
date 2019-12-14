<?php

// function that works with sent order data
function order_function()
{
    $body = getBodyAsObject();

    print_r($body);

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
    //   "rooms":"4",
    //   "baths":"2",
    //   "cleaningType":"spring-cleaning",
    //   "selectedExtras":{
    //      "windows":"3",
    //      "oven":1,
    //      "cooker-hood":1,
    //      "kic":1,
    //      "dishes":1,
    //      "optimisation":1
    //   },
    //   "date":"2019-12-19T12:12",
    //   "customer":{
    //      "name":"Алексей",
    //      "phone":" 375 25 615 17 88",
    //      "address":"Космонавтов",
    //      "email":"mymail@gamil.com"
    //   },
    //   "approximateCost":185,
    //   "approximateTime":8,
    //   "frequency":"monthly",
    //   "hasVacuumCleaner":"true"
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

    return "";
}

/**
 * Convert extras from bare id array to readable array of values
 * @param array $extrasArray
 * @return array
 */
function extrasToReadableArray(array $extrasArray)
{
    $result = array();

    foreach ($extrasArray as $item) {
        $value = "";

        if ($item === "windows") $value = "Мойка Окон";
        if ($item === "fridge") $value = "Чистка холодильника (или морозильной камеры) изнутри";
        if ($item === "microwave-oven") $value = "Чистка микроволновой печи";
        if ($item === "oven") $value = "Чистка духовки";
        if ($item === "cooker-hood") $value = "Чистка кухонной вытяжки";
        if ($item === "kic") $value = "Уборка кухонных шкафчиков";
        if ($item === "dishes") $value = "Мойка посуды";
        if ($item === "balcony") $value = "Уборка балкона";
        if ($item === "ironing") $value = "Глажка вещей";
        if ($item === "optimisation") $value = "Оптимизация пространства шкафов";

        $result[] = $value;
    }

    return $result;
}

?>