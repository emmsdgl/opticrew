-- Essential data restoration script
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- Clear existing data
TRUNCATE TABLE users;
TRUNCATE TABLE employees;
TRUNCATE TABLE clients;
TRUNCATE TABLE contracted_clients;
TRUNCATE TABLE locations;
TRUNCATE TABLE cars;
TRUNCATE TABLE holidays;
TRUNCATE TABLE company_settings;

INSERT INTO `users` (`id`, `name`, `username`, `email`, `profile_picture`, `phone`, `location`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'admin', 'admin@opticrew.com', 'profile_pictures/kMkdVeDfYpYvi9nC94Ohceut2lk14HMFnmgEqVXg.jpg', '+358 40 123 4567', 'Inari, Finland', NULL, '$2y$10$C/Y15/YOU5NHpf0zFtpsDO6RL2R.HMjTRi3C2rfA0eizMVOwEBvq2', 'admin', NULL, '2025-10-02 18:51:46', '2025-10-25 06:42:28', NULL),
(2, 'Vincent Rey Digol', 'vince123', 'vincentreydigol@finnoys.com', 'profile_pictures/IDMjQjtw1oK4v43oJLaET1NlUk863qV9mMM0mFgh.png', '+358 40 123 4567', 'Inari, Finland', NULL, '$2y$10$oo44Ffamrr.hoI349F5rzOVkuiDEKbKNHX9Q/DFlZRNLOSCSKYBCW', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-29 02:24:13', NULL),
(3, 'Martin Yvann Leonardo', 'martin123', 'martinyvannleonardo@finnoys.com', NULL, '+358 40 123 4567', 'Inari, Finland', NULL, '$2y$10$fEn6ftE4hV6qwLE6Pu7i5uTTpwHeEKyXtviZ7oTI7mh2hKzE660GS', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-25 06:42:00', NULL),
(4, 'Earl Leonardo', NULL, 'earlleonardo@finnoys.com', NULL, NULL, NULL, NULL, '$2y$10$UyZcyuwzjz1SrB6dPNZ3J.hCGWcCJ9ixAA0NP59Y7n9tkR/1JGdDW', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(5, 'Merlyn Guzman', NULL, 'merlynguzman@finnoys.com', 'profile_pictures/WG6Bb2Nyls3YKAPu0wLSGdAMA2Vk5ysLMqlSEyhr.jpg', '+358 40 123 4567', 'Inari, Finland', NULL, '$2y$10$KpT8QxYAqhp9HwVke31Wz.TEFENNoGFJQFdjhKUyyDJnFccFetEBi', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-25 12:04:23', NULL),
(6, 'Aries Guzman', NULL, 'ariesguzman@finnoys.com', NULL, NULL, NULL, NULL, '$2y$10$h85peir4XiLSdbJKuonExOoegZi/SBqQRGQ0xSYR5CzaERjEa2N3S', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(7, 'Bella Ostan', NULL, 'bellaostan@finnoys.com', NULL, NULL, NULL, NULL, '$2y$10$Zwd38lfJ4T5iG8JVXSxhBusbyZG/V4UWW8CePueSyXBh/IsY9D2Ju', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(8, 'Jennylyn Saballero', NULL, 'jennylynsaballero@finnoys.com', NULL, NULL, NULL, NULL, '$2y$10$zFxo49djPY3wMpN51qiYiOVxkkofeQ0RYRROfaphiP.cfs5BvHMQq', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(9, 'Rizza Estrella ', NULL, 'rizzaestrella@finnoys.com', NULL, NULL, NULL, NULL, '$2y$10$eH52zyHzV/ka6DJ1I/jIEegO6v7Bhtz3X6Tu51W7qzOCzg3.weuBy', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(10, 'Cherrylyn Morales ', NULL, 'cherrylynmorales@finnoys.com', NULL, NULL, NULL, NULL, '$2y$10$1swK95thyuDIKhCj1CDFJ.T591GF9ysJ4KvKJRaTfAiV7QTdU9Yaa', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(11, 'John Carl Morales', NULL, 'johncarlmorales@finnoys.com', NULL, NULL, NULL, NULL, '$2y$10$rLfpy6PWrrJYqVOkZUyFa.cLOLrTwLbKfkdp7tAS/vLQUotLTYOhi', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(12, 'John Kevin Morales', NULL, 'johnkevinmorales@finnoys.com', NULL, NULL, NULL, NULL, '$2y$10$/CV5zvi4rk3qSqg6JuYsme9FU7O06qgGq2eKBtDTSJXGhSPRAUAt.', 'employee', NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(17, 'Miradel Leonardo', 'mira123', 'miradel@gmail.com', NULL, '0401234567', 'Inari, Finland', NULL, '$2y$10$ogpFC.oYTxztWlLor7uXaecaHk9TA6jOrwk3DIOIt5TTQFAjFFB0C', 'external_client', 'JKByX8lUV6wGDkojeQw622DCRSgUgz5MIIMTRxsf3Nk6MC7F1XTH56ja0gR0', '2025-10-26 07:09:50', '2025-10-27 12:19:57', NULL),
(19, 'Kakslauttanen', 'kakslauttanen', 'kakslauttanen@company.com', NULL, '+358 00 000 0000', NULL, '2025-10-29 07:25:47', '$2y$10$caOzaZwOF315fH011mFFJegP0nJTHhXbHzXguXH/92NwCTh/zCx0y', 'company', NULL, '2025-10-29 07:25:47', '2025-10-29 07:25:47', NULL),
(20, 'Aikamatkat', 'aikamatkat', 'aikamatkat@company.com', NULL, '+358 00 000 0000', NULL, '2025-10-29 07:25:47', '$2y$10$Ne4U0KoxXs3KpJe.8Gob/e00tfBGmYDt/RogDhlB36eWpglZNC7bW', 'company', NULL, '2025-10-29 07:25:47', '2025-10-29 07:25:47', NULL);
INSERT INTO `employees` (`id`, `user_id`, `skills`, `is_active`, `is_day_off`, `is_busy`, `efficiency`, `has_driving_license`, `years_of_experience`, `salary_per_hour`, `created_at`, `updated_at`, `months_employed`, `deleted_at`) VALUES
(1, 2, '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 1, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(2, 3, '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 1, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(3, 4, '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 1, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(4, 5, '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 1, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(5, 6, '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 1, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(6, 7, '[\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(7, 8, '[\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(8, 9, '[\"Cleaning\"]', 1, 0, 0, 1.00, 0, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(9, 10, '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 1, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(10, 11, '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 1, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL),
(11, 12, '[\"Driving\",\"Cleaning\"]', 1, 0, 0, 1.00, 1, 0, 13.00, '2025-10-02 18:51:46', '2025-10-17 03:40:36', 0, NULL);
INSERT INTO `contracted_clients` (`id`, `user_id`, `name`, `email`, `phone`, `address`, `business_id`, `contract_start`, `contract_end`, `latitude`, `longitude`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 19, 'Kakslauttanen', 'kakslauttanen@company.com', '+358 00 000 0000', 'Address to be updated', '1234567-8', NULL, NULL, 68.33470361, 27.33426652, '2025-10-02 18:51:46', '2025-10-29 07:25:47', NULL),
(2, 20, 'Aikamatkat', 'aikamatkat@company.com', '+358 00 000 0000', 'Address to be updated', '1234567-0', NULL, NULL, 14.52682705, 121.01600925, '2025-10-02 18:51:46', '2025-10-29 07:25:47', NULL);
INSERT INTO `locations` (`id`, `contracted_client_id`, `location_name`, `location_type`, `base_cleaning_duration_minutes`, `normal_rate_per_hour`, `sunday_holiday_rate`, `deep_cleaning_rate`, `light_deep_cleaning_rate`, `student_rate`, `student_sunday_holiday_rate`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Small Cabin #1', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(2, 1, 'Small Cabin #2', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(3, 1, 'Small Cabin #3', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(4, 1, 'Small Cabin #4', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(5, 1, 'Small Cabin #5', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(6, 1, 'Small Cabin #6', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(7, 1, 'Small Cabin #7', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(8, 1, 'Small Cabin #8', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(9, 1, 'Small Cabin #9', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(10, 1, 'Small Cabin #10', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(11, 1, 'Small Cabin #11', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(12, 1, 'Small Cabin #12', 'Small Cabin', 60, 42.00, 84.00, NULL, NULL, 2.00, NULL, '2025-10-02 18:51:46', '2025-10-29 22:34:30', NULL),
(13, 1, 'Medium Cabin #1', 'Medium Cabin', 60, 51.00, 102.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(14, 1, 'Medium Cabin #2', 'Medium Cabin', 60, 51.00, 102.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(15, 1, 'Medium Cabin #3', 'Medium Cabin', 60, 51.00, 102.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(16, 1, 'Medium Cabin #4', 'Medium Cabin', 60, 51.00, 102.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(17, 1, 'Medium Cabin #5', 'Medium Cabin', 60, 51.00, 102.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(18, 1, 'Medium Cabin #6', 'Medium Cabin', 60, 51.00, 102.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(19, 1, 'Big Cabin #1', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(20, 1, 'Big Cabin #2', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(21, 1, 'Big Cabin #3', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(22, 1, 'Big Cabin #4', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(23, 1, 'Big Cabin #5', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(24, 1, 'Big Cabin #6', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(25, 1, 'Big Cabin #7', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(26, 1, 'Big Cabin #8', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(27, 1, 'Big Cabin #9', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(28, 1, 'Big Cabin #10', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(29, 1, 'Big Cabin #11', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(30, 1, 'Big Cabin #12', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(31, 1, 'Big Cabin #13', 'Big Cabin', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(32, 1, 'Queen Suite #1', 'Queen Suite', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(33, 1, 'Queen Suite #2', 'Queen Suite', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(34, 1, 'Queen Suite #3', 'Queen Suite', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(35, 1, 'Queen Suite #4', 'Queen Suite', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(36, 1, 'Queen Suite #5', 'Queen Suite', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(37, 1, 'Igloo #1', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(38, 1, 'Igloo #2', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(39, 1, 'Igloo #3', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(40, 1, 'Igloo #4', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(41, 1, 'Igloo #5', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(42, 1, 'Igloo #6', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(43, 1, 'Igloo #7', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(44, 1, 'Igloo #8', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(45, 1, 'Igloo #9', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(46, 1, 'Igloo #10', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(47, 1, 'Igloo #11', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(48, 1, 'Igloo #12', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(49, 1, 'Igloo #13', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(50, 1, 'Igloo #14', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(51, 1, 'Igloo #15', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(52, 1, 'Igloo #16', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(53, 1, 'Igloo #17', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(54, 1, 'Igloo #18', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(55, 1, 'Igloo #19', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(56, 1, 'Igloo #20', 'Igloo', 45, 30.00, 60.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(57, 1, 'Traditional House', 'Traditional House', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(58, 1, 'Turf Chamber', 'Turf Chamber', 60, 60.00, 120.00, NULL, NULL, NULL, NULL, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(59, 2, 'Panimo Cabins #1', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(60, 2, 'Panimo Cabins #2', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(61, 2, 'Panimo Cabins #3', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(62, 2, 'Panimo Cabins #4', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(63, 2, 'Panimo Cabins #5', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(64, 2, 'Panimo Cabins #6', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(65, 2, 'Panimo Cabins #7', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(66, 2, 'Panimo Cabins #8', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(67, 2, 'Panimo Cabins #9', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(68, 2, 'Panimo Cabins #10', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(69, 2, 'Panimo Cabins #11', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(70, 2, 'Panimo Cabins #12', 'Panimo Cabins', 60, 68.25, 120.50, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(71, 2, 'Metsakoti A', 'Metsakoti A', 60, 84.00, 126.00, 210.00, 110.00, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(72, 2, 'Metsakoti B', 'Metsakoti B', 60, 84.00, 126.00, 210.00, 110.00, 47.25, 70.90, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(73, 2, 'Kermikkas', 'Kermikkas', 60, 36.75, 55.50, NULL, NULL, 20.00, 30.00, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(74, 2, 'Hirvasaho A2 and B1', 'Hirvasaho A2 and B1', 60, 36.75, 55.50, NULL, NULL, 20.00, 30.00, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(75, 2, 'Hirvasaho B2', 'Hirvasaho B2', 60, 68.25, 102.50, NULL, NULL, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(76, 2, 'Hirvas Apartments', 'Hirvas Apartments', 60, 36.75, 55.50, NULL, NULL, 20.00, 30.00, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(77, 2, 'Voursa 3A and 3B', 'Voursa 3A and 3B', 60, 36.75, 55.50, NULL, NULL, 20.00, 30.00, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(78, 2, 'Voursa 3C', 'Voursa 3C', 60, 68.25, 102.50, NULL, NULL, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(79, 2, 'Moitakuru C31 and C32', 'Moitakuru C31 and C32', 60, 57.75, 87.50, NULL, NULL, 31.50, 47.25, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(80, 2, 'Luulampi', 'Luulampi', 60, 68.25, 102.50, NULL, NULL, 36.75, 55.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(81, 2, 'Metashirvas', 'Metashirvas', 60, 73.50, 110.25, NULL, NULL, 39.75, 59.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(82, 2, 'Kelotähti', 'Kelotähti', 60, 73.50, 110.25, NULL, NULL, 39.75, 59.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL),
(83, 2, 'Raahenmaja', 'Raahenmaja', 60, 94.50, 141.75, NULL, NULL, 51.00, 76.50, '2025-10-02 18:51:46', '2025-10-02 18:51:46', NULL);
INSERT INTO `clients` (`id`, `user_id`, `first_name`, `last_name`, `middle_initial`, `birthdate`, `security_question_1`, `security_answer_1`, `security_question_2`, `security_answer_2`, `created_at`, `updated_at`, `client_type`, `company_name`, `email`, `phone_number`, `business_id`, `street_address`, `postal_code`, `city`, `district`, `address`, `billing_address`, `einvoice_number`, `is_active`, `deleted_at`) VALUES
(5, 17, 'Miradel', 'Leonardo', 'L', '2000-06-06', 'pet_name', '$2y$10$N1TSNGAE3EKStIQ96.cNhObwOz2.dHqTlGetNiFJ/IF21x.tIZJkC', 'best_friend', '$2y$10$gxJFVyG.NplpvpOn56rjMuB9k.MQ86RmVRLo769REtBET9tYbOQcS', '2025-10-26 07:09:50', '2025-10-27 05:36:19', 'personal', NULL, NULL, NULL, NULL, 'Moisiontie 225', '33721', 'Tampere', 'Moisio', 'Moisiontie 225, 33721 Tampere, Moisio', 'Moisiontie 225, 33721 Tampere', NULL, 1, NULL),
(8, NULL, 'Miradel', 'Leonardo', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-26 11:02:50', '2025-10-26 11:02:50', 'company', 'ABCD Company', 'company@gmail.com', '0401234567', '12346', 'Korkeakoulunkatu', '13100', 'Hämeenlinna', 'Myllymäki', 'Korkeakoulunkatu, 13100 Hämeenlinna, Myllymäki', 'Korkeakoulunkatu, 13100 Hämeenlinna, Myllymäki', '789456', 0, NULL);
INSERT INTO `cars` (`id`, `car_name`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Van 1', 1, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(2, 'Van 2', 1, '2025-10-02 18:51:46', '2025-10-02 18:51:46'),
(3, 'Sedan 1', 1, '2025-10-02 18:51:46', '2025-10-02 18:51:46');
INSERT INTO `holidays` (`id`, `date`, `name`, `created_by`, `created_at`, `updated_at`) VALUES
(5, '2025-09-10', 'Special Holiday', 1, '2025-10-26 14:04:03', '2025-10-26 14:04:03'),
(6, '2025-09-11', 'Non-Working Holiday', 1, '2025-10-26 14:04:14', '2025-10-26 14:04:14'),
(7, '2025-09-12', 'Everyday Holiday', 1, '2025-10-26 14:04:25', '2025-10-26 14:04:25');
INSERT INTO `company_settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'office_latitude', '0', 'decimal', 'Office location latitude coordinate', '2025-10-25 11:09:20', '2025-10-25 11:09:20'),
(2, 'office_longitude', '0', 'decimal', 'Office location longitude coordinate', '2025-10-25 11:09:20', '2025-10-25 11:09:20'),
(3, 'geofence_radius', '110', 'integer', 'Geofence radius in meters for clock in/out', '2025-10-25 11:09:20', '2025-10-25 11:09:20');

SET FOREIGN_KEY_CHECKS = 1;
SELECT 'Essential data restored\!' AS Status;
