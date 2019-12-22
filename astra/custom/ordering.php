<?php

include 'email_sender.php';

/**
 * Main function that fires up processing of an order
 * <br>
 * If you want to show some results or debug it - use output functions like 'echo' or 'print_r()'
 */
function order_function()
{
    // get body of request
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
    $orderId = saveOrderToDB($body, $customerId, $connection);

    $connection->commit();

    // Show results of saving on frontend
    echo "Ваш заявка была принята!\nВскоре наш оператор свяжется с Вами по указанным контактным данным :)";

    // Send mail to operator
    sendMail($orderId, $connection);

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
 * Save customer if not exists and return its id
 * @param stdClass $customer [new] customer
 * @param mysqli $connection
 * @return int [new] customer id
 */
function saveCustomerToDB(stdClass $customer, mysqli $connection)
{
    // check if such customer exists -> get its id
    $sql = "SELECT * FROM " . CUSTOMERS_TABLE .
        " WHERE name='$customer->name' 
                AND phone='$customer->phone' 
                AND address='$customer->address'";

    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            return $row["id"];
        }
    }

    // save to db if not exists and get its id\
    $columns = "name, phone, address, email";
    $values = "'$customer->name', '$customer->phone', '$customer->address', '$customer->email'";

    $sql = "INSERT INTO " . CUSTOMERS_TABLE . " ($columns) VALUES ($values)";

    $customerId = null;

    if ($connection->query($sql) === TRUE) {
        $customerId = $connection->insert_id;
    } else {
        echo "Error: " . $sql . "<br>" . $connection->error;
    }

    return $customerId;
}

/**
 * Save order and return its id
 * @param stdClass $body body of order
 * @param int $customerId customer id
 * @param mysqli $connection
 * @return mixed|string|null saved order id
 */
function saveOrderToDB(stdClass $body, $customerId, mysqli $connection)
{
    // Create order in orders table
    $columns = "cleaning_type, customer, order_date, frequency";

    $values = "'$body->cleaningType', '$customerId', '$body->date', '$body->frequency'";

    $sql = "INSERT INTO " . ORDERS_TABLE . " ($columns) VALUES ($values)";

    $orderId = null;

    if ($connection->query($sql) === TRUE) {
        $orderId = $connection->insert_id;
    } else {
        return "Error: $sql <br> $connection->error";
    }

    // Save to many-to-many table
    $services = (array)$body->selectedExtras;
    $services['rooms'] = $body->rooms;
    $services['baths'] = $body->baths;

    $isVacuumCleanerNeeded = 1;

    if ($body->hasVacuumCleaner === true) {
        $isVacuumCleanerNeeded = 0;
    }

    $services['vacuum_cleaner'] = $isVacuumCleanerNeeded;

    $columns = "order_id, service_id, amount";

    // TODO save to order_services table all the rows
    foreach ($services as $service => $amount) {
        $values = "'$orderId', '$service', '$amount'";

        $sql = "INSERT INTO " . ORDER_SERVICES_TABLE . " ($columns) VALUES ($values)";

        if ($connection->query($sql) !== TRUE) {
            return "Error: $sql <br> $connection->error";
        }
    }

    return $orderId;
}

/**
 * Calculates summarising data for given order
 * @param array $orderData main order data
 * @param array $orderServices services included in order
 * @return array named array of sums calculated for given order
 */
function calculateOrderSummary($orderData, $orderServices)
{

    $totalCost = 0;
    $totalTimeHours = 0;

    // calculate sums of an order
    foreach ($orderServices as $service) {
        $price = $service[FIELD_OS_PRICE["f"]];
        $duration = $service[FIELD_OS_DURATION["f"]];
        $amount = $service[FIELD_AMOUNT["f"]];

        $totalCost += $price * $amount;
        $totalTimeHours += $duration * $amount;
    }

    // round up total time in hours to int value
    $totalTimeHours = round($totalTimeHours);

    // calculate required amount of masters
    $workingDayHours = 8;
    $requiredMastersAmount = 1 + intdiv($totalTimeHours, $workingDayHours);

    return array("totalCost" => $totalCost,
        "totalTimeHours" => $totalTimeHours,
        "requiredMastersAmount" => $requiredMastersAmount);
}

?>