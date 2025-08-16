DROP DATABASE IF EXISTS securebank;
CREATE DATABASE securebank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE securebank;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  phone VARCHAR(15) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer'
);

CREATE TABLE accounts (
  account_no BIGINT PRIMARY KEY,
  user_id INT NOT NULL,
  balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  account_no BIGINT NOT NULL,
  description VARCHAR(255),
  amount DECIMAL(12,2) NOT NULL,
  date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  balance_after DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (account_no) REFERENCES accounts(account_no) ON DELETE CASCADE
);

INSERT INTO users (username, phone, email, password, role)
VALUES ('admin', '0000000000', 'admin@securebank.com', 'admin123', 'admin');

INSERT INTO users (username, phone, email, password, role)
VALUES 
('alice', '1111111111', 'alice@example.com', 'alice123', 'customer'),
('bob',   '2222222222', 'bob@example.com',   'bob123',   'customer');

INSERT INTO accounts (account_no, user_id, balance)
VALUES 
(10001, 2, 5000.00),
(10002, 3, 3000.00);

INSERT INTO transactions (account_no, description, amount, balance_after)
VALUES
(10001, 'Initial Deposit', 5000.00, 5000.00),
(10002, 'Initial Deposit', 3000.00, 3000.00);