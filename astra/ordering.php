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
    $orderId = saveOrderToDB($body, $customerId, $connection);

    $connection->commit();

    // Show results of saving on frontend
    echo "Ваш заявка была принята!\nВскоре наш оператор свяжется с Вами по указанным контактным данным :)";

    sendMail($orderId, $connection);

    // Close the connection
    $connection->close();

    // action finished
    die;
}

function sendMail($orderId, mysqli $connection)
{
    // Read from db everything about an order EXCEPT services
    $orderData = getOrderData($orderId, $connection);

    // Read from db order services for order
    $orderServices = getServicesForOrder($orderId, $connection);

    // TODO prepare message from obtained order data
    $message = prepareMessage($orderData, $orderServices);

    // TODO send message
//    sendMessage($message);
}

/**
 * Build message from obtained order data
 * @param array $orderData order data
 * @param array $orderServices order services
 * @return string resulting message
 */
function prepareMessage(array $orderData, array $orderServices)
{
    return "";
}

/**
 * Send prepared message to operator
 * @param $message
 */
function sendMessage($message)
{
    $emailAddress = 'name @ company . com';
    $subject = 'Новый заказ';
    $headers = 'From: noreply@уберем.бел';

    $secureCheck = sanitizeEmail($emailAddress);

    if ($secureCheck == false) {
        echo "Неправильные данные для письма!";
    } else { //send email
        mail($emailAddress, $subject, $message, $headers);
        echo "Письмо отправлено.";
    }
}

/**
 * Check field for email
 * @param $field
 * @return bool
 */
function sanitizeEmail($field)
{
    $field = filter_var($field, FILTER_SANITIZE_EMAIL);
    return filter_var($field, FILTER_VALIDATE_EMAIL);
}

/**
 * Read from db order services for order
 * @param $orderId
 * @param mysqli $connection
 * @return array
 */
function getServicesForOrder($orderId, mysqli $connection)
{
    $servicesTable = "services";
    $orderServicesTable = "order_services";

    $sql = "SELECT s.*, os.amount amount
            FROM $orderServicesTable os
            INNER JOIN ($servicesTable s)
            ON (os.service_id = s.id AND os.order_id = $orderId)
            GROUP BY s.id";

    $result = $connection->query($sql);

    $orderServices = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($orderServices, $row);
        }
    }

    return $orderServices;
}

/**
 * Read from db everything about an order EXCEPT services
 * @param $orderId
 * @param mysqli $connection
 * @return array|null
 */
function getOrderData($orderId, mysqli $connection)
{
    $orderTable = "orders";
    $cleaningTypesTable = "cleaning_types";
    $frequenciesTable = "frequencies";
    $customersTable = "customers";

    $sql = "SELECT 
                o.id order_id, o.dt_create dt_create, o.order_date order_date, 
                c.name name, c.phone phone, c.address address, c.email email, c.payload customer_payload,
                f.description frequency, f.discount frequency_discount,
                ct.title ct_title
            FROM $orderTable o
            INNER JOIN ($customersTable c , $cleaningTypesTable ct, $frequenciesTable f)
            ON (o.customer = c.id AND o.id=$orderId)
            GROUP BY o.id";

    $result = $connection->query($sql);

    $orderData = null;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orderData = $row;
        }
    }

    return $orderData;
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
    $table = "customers";

    // check if such customer exists -> get its id
    $sql = "SELECT * FROM $table WHERE name='$customer->name' AND phone='$customer->phone' AND address='$customer->address'";

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

    $sql = "INSERT INTO $table ($columns) VALUES ($values)";

    $customerId = null;

    if ($connection->query($sql) === TRUE) {
        $customerId = $connection->insert_id;
    } else {
        echo "Error: " . $sql . "<br>" . $connection->error;
    }

    return $customerId;
}

/**
 * @param stdClass $body
 * @param $customerId
 * @param mysqli $connection
 * @return mixed|string|null
 */
function saveOrderToDB(stdClass $body, $customerId, mysqli $connection)
{
    $ordersTable = "orders";
    $orderServicesTable = "order_services";

    // Create order in orders table
    $columns = "cleaning_type, customer, order_date, frequency";

    $values = "'$body->cleaningType', '$customerId', '$body->date', '$body->frequency'";

    $sql = "INSERT INTO $ordersTable ($columns) VALUES ($values)";

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
    $services['vacuum_cleaner'] = $body->hasVacuumCleaner ? 0 : 1;

    $columns = "order_id, service_id, amount";

    // TODO save to order_services table all the rows
    foreach ($services as $service => $amount) {
        $values = "'$orderId', '$service', '$amount'";

        $sql = "INSERT INTO $orderServicesTable ($columns) VALUES ($values)";

        if ($connection->query($sql) !== TRUE) {
            return "Error: $sql <br> $connection->error";
        }
    }

    return $orderId;
}

?>