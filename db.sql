CREATE TABLE "item" (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `name` TEXT,
    `qty` INTEGER,
    `category_id` INTEGER,
    `type`  TEXT,
    `location_id` INTEGER,

    FOREIGN KEY(`location_id`) REFERENCES location(id)
);

CREATE TABLE `category` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `name`  TEXT,
    `parent_id` INTEGER,
    FOREIGN KEY(`parent_id`) REFERENCES category(id)	
);

CREATE TABLE `location` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `name`  TEXT
);

CREATE TABLE `category_item` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `category_id`   INTEGER NOT NULL,
    `item_id`  INTEGER NOT NULL,

    FOREIGN KEY(`category_id`) REFERENCES category(id),
    FOREIGN KEY(`item_id`) REFERENCES item(id)
);

CREATE TABLE `photo` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `fname`  TEXT
);


CREATE TABLE `location_photo` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `location_id`   INTEGER NOT NULL,
    `photo_id`  INTEGER NOT NULL,

    FOREIGN KEY(`location_id`) REFERENCES location(id),
    FOREIGN KEY(`photo_id`) REFERENCES photo(id)
);

CREATE TABLE `item_photo` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    `item_id`   INTEGER NOT NULL,
    `photo_id`  INTEGER NOT NULL,

    FOREIGN KEY(`item_id`) REFERENCES item(id),
    FOREIGN KEY(`photo_id`) REFERENCES photo(id)
);
