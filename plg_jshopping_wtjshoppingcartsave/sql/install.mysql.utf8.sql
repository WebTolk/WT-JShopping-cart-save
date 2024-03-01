CREATE TABLE `#__plg_jshopping_wtjshoppingcartsave`
(
    `id`            int          NOT NULL AUTO_INCREMENT,
    `temp_cart_id`  varchar(255) NOT NULL UNIQUE COMMENT 'JoomShopping temp cart id',
    `date_modified` datetime     NOT NULL,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET = utf8;