<?php
add_action('wp_ajax_order', 'order_function'); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action('wp_ajax_nopriv_order', 'order_function');  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей

function order_function()
{
    $body = $_POST['body'];
    echo $body;

    die; // даём понять, что обработчик закончил выполнение
}

?>