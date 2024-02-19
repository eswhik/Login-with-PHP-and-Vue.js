CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  last_name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  image varchar(255) DEFAULT NULL,
  status varchar(50) DEFAULT 'activo',
  creation_date timestamp NOT NULL DEFAULT current_timestamp(),
  update_date timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);
