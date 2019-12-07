CREATE TABLE order_data
(
    id               INT PRIMARY KEY AUTO_INCREMENT,
    rooms            SMALLINT    NOT NULL,
    baths            SMALLINT    NOT NULL,
    cleaning_type    VARCHAR(50) NOT NULL,
    selected_extras  TEXT,
    customer         TEXT        NOT NULL,
    frequency        VARCHAR(50) NOT NULL,
    approximate_cost FLOAT       NOT NULL,
    approximate_time VARCHAR(2)  NOT NULL
);