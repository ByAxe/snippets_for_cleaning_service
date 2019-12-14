DROP TABLE IF EXISTS customers CASCADE;
CREATE TABLE customers
(
    id      INT PRIMARY KEY AUTO_INCREMENT,
    name    VARCHAR(255) NOT NULL,
    phone   VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    email   VARCHAR(255),
    payload TEXT
);


DROP TABLE IF EXISTS orders CASCADE;
CREATE TABLE orders
(
    id                 INT PRIMARY KEY AUTO_INCREMENT,
    rooms              SMALLINT  NOT NULL,
    baths              SMALLINT  NOT NULL,
    cleaning_type      INT       NOT NULL REFERENCES cleaning_types,
    customer           INT       NOT NULL REFERENCES customers,
    date               TIMESTAMP NOT NULL,
    frequency          INT       NOT NULL REFERENCES frequencies,
    has_vacuum_cleaner BOOLEAN   NOT NULL DEFAULT FALSE
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
    id          INT PRIMARY KEY AUTO_INCREMENT,
    title       VARCHAR(50) NOT NULL,
    description TEXT
);


DROP TABLE IF EXISTS services CASCADE;
CREATE TABLE services
(
    id             VARCHAR(50) PRIMARY KEY,
    title          VARCHAR(50) NOT NULL,
    description    VARCHAR(255),
    price          DECIMAL     NOT NULL,
    duration_hours FLOAT       NOT NULL,
    is_extra       BOOLEAN     NOT NULL DEFAULT FALSE,
    available      BOOLEAN     NOT NULL DEFAULT TRUE
);

DROP TABLE IF EXISTS order_services CASCADE;
CREATE TABLE order_services
(
    order_id   INT         NOT NULL REFERENCES orders,
    service_id VARCHAR(50) NOT NULL REFERENCES services,
    amount     FLOAT,
    PRIMARY KEY (order_id, service_id)
);

INSERT INTO cleaning_types (title, description)
VALUES ('Генеральная', '<li><b>Генеральную уборку</b>, согласно рекомендациям специалистов,
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
       ('Классическая', '<li><b>Классическая уборка</b>, это вид уборки, при котором наша компания выполняет все
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
       ('fridge', 'Чистка холодильника', '', 12.0, 0.25, true, true),
       ('microwave-oven', 'Чистка микроволновой печи', '', 8.0, 0.25, true, true),
       ('oven', 'Чистка духового шкафа', '', 15.0, 0.5, true, true),
       ('cooker-hood', 'Чистка кухонной вытяжки', '', 15.0, 0.5, true, true),
       ('kic', 'Уборка кухонных шкафчиков', '', 18.0, 0.75, true, true),
       ('dishes', 'Мойка посуды', '', 10.0, 0.4, true, true),
       ('balcony', 'Уборка балкона', '', 12.0, 0.4, true, true),
       ('ironing', 'Глажка вещей', '', 10.0, 0.5, true, true),
       ('optimisation', 'Отптимизация внутренного пространства шкафов', '', 10.0, 0.5, true, true),
       ('vacuum_cleaner', 'Использование пылесоса компании', '', 5.0, 0.0, true, true);


INSERT INTO frequencies (id, discount, description)
VALUES ('once', 0.0, 'Разовая'),
       ('monthly', 10.0, 'Раз в месяц'),
       ('twoweekly', 15.0, 'Раз в две недели'),
       ('weekly', 20.0, 'Раз в неделю');