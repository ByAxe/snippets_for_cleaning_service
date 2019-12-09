DROP TABLE IF EXISTS customers CASCADE;
CREATE TABLE customers
(
    id      INT PRIMARY KEY AUTO_INCREMENT,
    name    VARCHAR(255) NOT NULL,
    phone   VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    email   VARCHAR(255)
);


DROP TABLE IF EXISTS order_data CASCADE;
CREATE TABLE order_data
(
    id               INT PRIMARY KEY AUTO_INCREMENT,
    rooms            SMALLINT    NOT NULL,
    baths            SMALLINT    NOT NULL,
    cleaning_type    VARCHAR(50) NOT NULL,
    selected_extras  TEXT,
    customer_id      INT         NOT NULL REFERENCES customers,
    date             TIMESTAMP   NOT NULL,
    frequency        VARCHAR(50) NOT NULL,
    approximate_cost FLOAT       NOT NULL,
    approximate_time VARCHAR(2)  NOT NULL
);