CREATE TABLE users (
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    created_by integer,
    updated_by integer,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL,
    deleted_at timestamp NULL,
    last_connexion_at timestamp NULL,
    is_deleted boolean NOT NULL DEFAULT false,
    username varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    password varchar(255) NOT NULL,
    role_code varchar(10) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `created_at`, `is_deleted`, `username`, `email`, `password`, `role_code`) VALUES
(1, CURRENT_TIMESTAMP, false, "MaloMartin", "MaloMartin@kaliAndCo.fr", "$2y$10$zUbf.ZWx6LWSGWbDTGfnxu38kP0vSjV8SZivONm3It50tJTlp6HIG", "ADMIN"),
(2, CURRENT_TIMESTAMP, false, "KainaMedroub", "KainaMedroub@kaliAndCo.fr", "$2y$10$zUbf.ZWx6LWSGWbDTGfnxu38kP0vSjV8SZivONm3It50tJTlp6HIG", "USER"),
(3, CURRENT_TIMESTAMP, false, "SoumayaMougamadou", "Soumaya.M@kaliAndCo.fr", "$2y$10$zUbf.ZWx6LWSGWbDTGfnxu38kP0vSjV8SZivONm3It50tJTlp6HIG", "USER"),
(4, CURRENT_TIMESTAMP, false, "VanessaChallalPereira", "Vanessa.C.P@kaliAndCo.fr", "$2y$10$zUbf.ZWx6LWSGWbDTGfnxu38kP0vSjV8SZivONm3It50tJTlp6HIG", "USER"),
(5, CURRENT_TIMESTAMP, false, "NinaDaSilva", "NinaDaSilva@kaliAndCo.fr", "$2y$10$zUbf.ZWx6LWSGWbDTGfnxu38kP0vSjV8SZivONm3It50tJTlp6HIG", "USER"),
(6, CURRENT_TIMESTAMP, false, "ThomasCharvot", "ThomasCharvot@kaliAndCo.fr", "$2y$10$zUbf.ZWx6LWSGWbDTGfnxu38kP0vSjV8SZivONm3It50tJTlp6HIG", "USER"),
(7, CURRENT_TIMESTAMP, false, "NassimAissaoui", "NassimAissaoui@kaliAndCo.fr", "$2y$10$zUbf.ZWx6LWSGWbDTGfnxu38kP0vSjV8SZivONm3It50tJTlp6HIG", "ADMIN");
/*
ETC ....
*/
