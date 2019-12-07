<?php

// function that works with sent order data
function order_function()
{
    //{
    //   "orderData":{
    //      "rooms":"2",
    //      "baths":"1",
    //      "cleaningType":"classic-cleaning",
    //      "selectedExtras":[
    //         "order-form-extras-windows",
    //         "order-form-extras-optimisation",
    //         "order-form-extras-kitchen-inside-cabinets",
    //         "order-form-extras-ironing"
    //      ],
    //      "customer":{
    //         "name":"My name",
    //         "date":"2019-12-12T09:09",
    //         "phone":" 375256151788",
    //         "address":"г. Минск, Космонавтов 47",
    //         "email":"mymail@gmail.com"
    //      },
    //   },
    //   "approximateCost": 120,
    //   "approximateTime": 6,
    //   "frequency":"twoweekly",
    //}
    $body = $_POST['body'];

    // Save data to database
    $host = "178.124.139.19";
    $user = "c19356_wp1";
    $password = "L(QK&isRkTBpoH1y0R(93](3";

    // Create connection
    $connection = new mysqli($host, $user, $password);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Close the connection
    $connection->close();

    // Send order to email


    die; // action finished
}

?>