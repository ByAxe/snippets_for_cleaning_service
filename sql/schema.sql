DROP TABLE IF EXISTS customers CASCADE;
CREATE TABLE customers
(
    id      INT PRIMARY KEY AUTO_INCREMENT,
    name    VARCHAR(255) NOT NULL,
    phone   VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    email   VARCHAR(255),
    payload TEXT,
    CONSTRAINT customer_uniqueness UNIQUE (name, phone, address)
);

DROP TABLE IF EXISTS frequencies CASCADE;
CREATE TABLE frequencies
(
    id          VARCHAR(50) PRIMARY KEY,
    discount    DECIMAL NOT NULL DEFAULT 0.0,
    description TEXT
);


DROP TABLE IF EXISTS cleaning_types CASCADE;
CREATE TABLE cleaning_types
(
    id          VARCHAR(50) PRIMARY KEY,
    title       VARCHAR(50) NOT NULL,
    description TEXT
);


DROP TABLE IF EXISTS orders CASCADE;
CREATE TABLE orders
(
    id            INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    cleaning_type VARCHAR(50)     NOT NULL,
    customer      INT             NOT NULL,
    order_date    TIMESTAMP       NOT NULL,
    frequency     VARCHAR(50)     NOT NULL,
    dt_create     TIMESTAMP       NOT NULL DEFAULT now(),
    FOREIGN KEY (cleaning_type) REFERENCES cleaning_types (id),
    FOREIGN KEY (customer) REFERENCES customers (id),
    FOREIGN KEY (frequency) REFERENCES frequencies (id)
);


DROP TABLE IF EXISTS services CASCADE;
CREATE TABLE services
(
    id             VARCHAR(50) PRIMARY KEY,
    title          VARCHAR(255) NOT NULL,
    description    TEXT,
    price          DECIMAL      NOT NULL,
    duration_hours FLOAT        NOT NULL,
    is_extra       BOOLEAN      NOT NULL DEFAULT FALSE,
    available      BOOLEAN      NOT NULL DEFAULT TRUE
);

DROP TABLE IF EXISTS order_services CASCADE;
CREATE TABLE order_services
(
    order_id   INT         NOT NULL,
    service_id VARCHAR(50) NOT NULL,
    amount     FLOAT       NOT NULL DEFAULT 1.0,
    PRIMARY KEY (order_id, service_id),
    FOREIGN KEY (order_id) REFERENCES orders (id),
    FOREIGN KEY (service_id) REFERENCES services (id)
);


# INSERT
INSERT INTO cleaning_types (id, title, description)
VALUES ('spring-cleaning', 'Генеральная', '<li><b>Генеральную уборку</b>, согласно рекомендациям специалистов,
                                следует проводить не реже чем <b>раз в 3 месяца</b>.
                                <br>Таким образом вы не дадите пыли скопиться и позаботитесь о своем здоровье,
                                насколько это возможно
                            </li>
                            <li><b>Генеральная уборка занимает дольше чем обычная</b>, потому что включает все
                                дополнительные меры по очистке помещений: мойку окон,
                                внутренних частей кухонной мебели и тд.
                            </li>
                            <li><b>Однако</b>, если вы из тех людей, для кого <i>чистота является особенно важным
                                критерием</i>
                                комфорта, то проводить ее можно <b>с частотой обычной убоки</b>,
                                ведь так вы всегда сможете
                                жить, как в абсолютно новой квартире
                            </li>'),
       ('classic-cleaning', 'Классическая', '<li><b>Классическая уборка</b>, это вид уборки, при котором наша компания выполняет все
                                указанные в <a href="#our-services-block">оказываемых услугах</a>
                                процедуры очистки помещений. По умолчанию доп. услуги не входят в этот перечень,
                                но <i>их можно добавить</i> когда вы заказываете наш сервис.
                            </li>
                            <li><b>Кроме стандарных моментов</b>, она включает в себя такие меры, как складывание
                                одежды, чистка плинтуса от грязи, мойка зеркал/санузлов и раковин и тд.
                            </li>
                            <li><b>Данный вид уборки</b>, у наших мастеров, занимает уже <b>меньше времени</b> чем
                                генеральная, но все еще это время варъируется в рамках пары-тройки часов, в зависимости
                                от помещения
                            </li>');


INSERT INTO services (id, title, description, price, duration_hours, is_extra, available)
VALUES ('rooms', 'Уборка комнаты', '', 14.0, 0.5, false, true),
       ('baths', 'Уборка санузла', '', 15.0, 0.5, false, true),
       ('windows', 'Мойка Окна', '', 8.0, 0.25, true, true),
       ('fridge', 'Чистка холодильника (или морозильной камеры) изнутри', '', 12.0, 0.25, true, true),
       ('microwave-oven', 'Чистка микроволновой печи', '', 8.0, 0.25, true, true),
       ('oven', 'Чистка духового шкафа', '', 15.0, 0.5, true, true),
       ('cooker-hood', 'Чистка кухонной вытяжки', '', 15.0, 0.5, true, true),
       ('kic', 'Уборка кухонных шкафчиков', '', 18.0, 0.75, true, true),
       ('dishes', 'Мойка посуды', '', 10.0, 0.4, true, true),
       ('balcony', 'Уборка балкона', '', 12.0, 0.5, true, true),
       ('ironing', 'Глажка вещей', '', 10.0, 0.5, true, true),
       ('optimisation', 'Отптимизация внутренного пространства шкафа', '', 10.0, 0.5, true, true),
       ('vacuum_cleaner', 'Использование пылесоса Компании', '', 5.0, 0.1, true, true),
       ('order_departure', 'Выезд на заказ', '', 16.0, 1, false, true);


INSERT INTO frequencies (id, discount, description)
VALUES ('once', 0.0, 'Разовая'),
       ('monthly', 10.0, 'Раз в месяц'),
       ('twoweekly', 15.0, 'Раз в две недели'),
       ('weekly', 20.0, 'Раз в неделю');