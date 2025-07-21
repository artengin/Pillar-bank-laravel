INSERT INTO users (id, first_name, last_name, ssn, phone, email, password, created_at, updated_at, status) VALUES
  (1, 'john', 'boms', '729-61-1829', '11111111', 'boms@gmail.com', 'password', '2016-10-20 11:05:00', '2016-10-20 11:05:00', 'approve'),
  (2, 'mike', 'adams', '111-61-1829', '22222222', 'adams@gmail.com', 'password', '2016-10-20 11:05:00', '2016-10-20 11:05:00', 'approve');

INSERT INTO "cards"(id, user_id, name, status, number, balance, finished_at, created_at, updated_at) VALUES
  (1, 1, 'BankCard', 'active', '1500001000300201', '0', '2016-11-11 11:11:11', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1, 'Card', 'active', '1500001000300202', '0', '2016-11-11 11:11:11', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 1, 'Card', 'active', '1500001000300203', '0', '2016-11-11 11:11:11', '2016-10-20 11:05:00', '2016-10-20 11:05:00');