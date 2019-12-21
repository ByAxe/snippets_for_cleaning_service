<?php

include 'email_data_converter.php';
include 'constants.php';

function sendMail($orderId, mysqli $connection)
{
    // Read from db everything about an order EXCEPT services
    $orderData = getOrderData($orderId, $connection);

    // Read from db order services for order
    $orderServices = getServicesForOrder($orderId, $connection);

    // prepare message from obtained order data
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
    $orderDataAsString = convertOrderDataToString($orderData);
    $orderServices = convertOrderServicesToString($orderServices);

    $message = combineOrderDataAndOrderServices($orderDataAsString, $orderServices);

    return $message;
}

function combineOrderDataAndOrderServices($orderDataAsString, $orderServices)
{
    $header = "<h1>Данные заказа</h1><br>";
    $servicesBreak = "<hr><h2>Услуги заказа</h2><br>";
    $message = $header . $orderDataAsString . $servicesBreak . $orderServices;

    return $message;

}

/**
 * Send prepared message to operator
 * @param $message
 */
function sendMessage($message)
{
    $subject = 'Новый заказ';
    $headers = 'From: noreply@уберем.бел';

    $secureCheck = sanitizeEmail(OPERATOR_EMAIL_ADDRESS);

    if ($secureCheck == false) {
        echo "Неправильные данные для письма!";
    } else { //send email
        mail(OPERATOR_EMAIL_ADDRESS, $subject, $message, $headers);
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
    $sql = "SELECT s.*, os.amount amount
            FROM " . ORDER_SERVICES_TABLE . " os
            INNER JOIN (" . SERVICES_TABLE . " s)
            ON (os.service_id = s.id AND os.order_id = $orderId)
            GROUP BY s.id";

    $result = $connection->query($sql);

    $orderServices = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($orderServices, $row);
        }
    }

    // add basic services to array
    addBasicServicesToArray($orderServices, $connection);

    return $orderServices;
}

function addBasicServicesToArray(array $orderServices, mysqli $connection)
{
    // TODO implement
}

/**
 * Read from db everything about an order EXCEPT services
 * @param $orderId
 * @param mysqli $connection
 * @return array|null
 */
function getOrderData($orderId, mysqli $connection)
{
    $sql = "SELECT o.id " . FIELD_ORDER_ID["f"] .
        ", o.dt_create " . FIELD_DT_CREATE["f"] .
        ", o.order_date " . FIELD_ORDER_DATE["f"] .
        ", c.name " . FIELD_CUSTOMER_NAME["f"] .
        ", c.phone " . FIELD_CUSTOMER_PHONE["f"] .
        ", c.address " . FIELD_CUSTOMER_ADDRESS["f"] .
        ", c.email " . FIELD_CUSTOMER_EMAIL["f"] .
        ", c.payload " . FIELD_CUSTOMER_PAYLOAD["f"] .
        ", f.description " . FIELD_FREQUENCY["f"] .
        ", f.discount " . FIELD_DISCOUNT["f"] .
        ", ct.title " . FIELD_CLEANING_TYPE["f"] .
        " FROM " . ORDERS_TABLE . " o
            INNER JOIN (" . CUSTOMERS_TABLE . " c , " . CLEANING_TYPES_TABLE . " ct, " . FREQUENCIES_TABLE . " f)
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