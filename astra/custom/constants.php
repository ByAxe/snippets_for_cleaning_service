<?php

// TABLE NAMES
define("SERVICES_TABLE", "services");
define("ORDERS_TABLE", "orders");
define("ORDER_SERVICES_TABLE", "order_services");
define("CLEANING_TYPES_TABLE", "cleaning_types");
define("FREQUENCIES_TABLE", "frequencies");
define("CUSTOMERS_TABLE", "customers");

// SELECTED ORDER FIELDS NAMES
define("FIELD_ORDER_ID", array("f" => "order_id", "n" => "Идентификатор заказа"));
define("FIELD_DT_CREATE", array("f" => "dt_create", "n" => "Дата и время размещения заказа"));
define("FIELD_ORDER_DATE", array("f" => "order_date", "n" => "Дата и время заказа"));
define("FIELD_FREQUENCY", array("f" => "frequency", "n" => "Желаемая частота заказов"));
define("FIELD_DISCOUNT", array("f" => "frequency_discount", "n" => "Скидка частоты (%)"));
define("FIELD_CLEANING_TYPE", array("f" => "ct_title", "n" => "Тип уборки"));
define("FIELD_CUSTOMER_NAME", array("f" => "name", "n" => "ФИО Клиента"));
define("FIELD_CUSTOMER_PHONE", array("f" => "phone", "n" => "Контактный телефон Клиента"));
define("FIELD_CUSTOMER_ADDRESS", array("f" => "address", "n" => "Адрес Клиента"));
define("FIELD_CUSTOMER_EMAIL", array("f" => "email", "n" => "Контактная Электронная Почта Клиента"));
define("FIELD_CUSTOMER_PAYLOAD", array("f" => "customer_payload", "n" => "Дополнительная информация о Клиенте"));

// SELECTED ORDER-SERVICES FIELDS NAMES
define("FIELD_OS_TITLE", array("f" => "title", "n" => "Наименование Услуги"));
define("FIELD_OS_DESCRIPTION", array("f" => "description", "n" => "Описание Услуги"));
define("FIELD_OS_PRICE", array("f" => "price", "n" => "Стоимость Услуги (BYN)"));
define("FIELD_OS_DURATION", array("f" => "duration_hours", "n" => "Длительность исполнения услуги (Часов)"));
define("FIELD_OS_IS_EXTRA", array("f" => "is_extra", "n" => "Дополнительная услуга?"));
define("FIELD_OS_IS_AVAILABLE", array("f" => "available", "n" => "Услуга доступна?"));
define("FIELD_AMOUNT", array("f" => "amount", "n" => "Количество"));


// BASIC SERVICES ID
define("BASIC_SERVICES_ID_LIST", array("order_departure"));

// EMAIL DATA
define("OPERATOR_EMAIL_ADDRESS", "johnkinsleygl@gmail.com");

define("PREMIUM_MULTIPLIER", 3);