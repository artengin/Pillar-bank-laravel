INSERT INTO users (id, first_name, last_name, phone, email, ssn, password, created_at, updated_at, status) VALUES
  (1, 'John', 'Doe', '0123456789', 'zhora@mail.ru', '111-11-1111', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00', 'approve');

INSERT INTO cards (id, user_id, name, number, balance, finished_at, created_at, updated_at) VALUES
  (1, 1, 'Mrs. Kelly Dicki V', 8017750303723171, 9, '2013-06-14 06:18:27', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO transactions (id, card_id, name, card_number, amount, type, created_at, updated_at) VALUES
  (1, 1, 'Amazon', 1, 1, 'outgoing', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

