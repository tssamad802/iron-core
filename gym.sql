-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2026 at 11:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'mark',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `member_id`, `status`, `created_at`) VALUES
(16, 104, 'mark', '2026-03-09 19:00:00'),
(17, 102, 'mark', '2026-03-09 19:00:00'),
(18, 101, 'mark', '2026-03-10 04:53:10'),
(19, 103, 'mark', '2026-03-10 05:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `diet`
--

CREATE TABLE `diet` (
  `id` int(11) NOT NULL,
  `diet_name` varchar(55) NOT NULL,
  `goal` varchar(55) NOT NULL,
  `calories` varchar(55) NOT NULL,
  `duration` int(55) NOT NULL,
  `breakfast` text DEFAULT NULL,
  `lunch` text DEFAULT NULL,
  `dinner` text DEFAULT NULL,
  `notes` text NOT NULL,
  `member_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diet`
--

INSERT INTO `diet` (`id`, `diet_name`, `goal`, `calories`, `duration`, `breakfast`, `lunch`, `dinner`, `notes`, `member_id`, `created_at`) VALUES
(2, 'Lean Cut Plan', 'Weight Loss', '2000', 10, 'Oatmeal, boiled eggs, apple', 'Grilled chicken, brown rice, broccoli', 'Tuna salad, whole wheat bread', 'Avoid sugary drinks. High protein diet.', 0, '2026-03-06 19:00:00'),
(4, 'Summer Cut Plan', 'Maintain Weight', '120', 2, 'Oats, boiled eggs, banana', 'Grilled chicken, brown rice, broccoli', 'Grilled chicken, quinoa, spinach', 'this is good', 101, '2026-03-08 19:00:00'),
(5, 'Muscle Gain Plan', 'Build Muscle', '2800', 12, 'Scrambled eggs, whole wheat toast, avocado', 'Grilled steak, quinoa, mixed veggies', 'Chicken breast, brown rice, spinach salad', 'Focus on protein intake, lift weights 4–5 times a week', 101, '2026-03-08 19:00:00'),
(7, 'Balanced Fitness Plan', 'Maintain Weight', '2500', 10, 'Greek yogurt, berries, granola', 'Turkey sandwich, carrot sticks, apple', 'Baked cod, sweet potato, broccoli', 'Mix cardio and strength training, track water intake\r\n', 104, '2026-03-08 19:00:00'),
(8, 'Titan Strength Fuel', 'Build Muscle', '3000', 8, '4 eggs (scrambled/boiled)  1 cup oats with milk + 1 ban', '150–200g grilled chicken breast  1 cup cooked rice or q', '150–200g salmon, beef, or chicken  1 medium sweet potat', 'Drink 3–4 liters of water daily.\r\n\r\nInclude multivitamin if needed.\r\n\r\nPost-workout: protein shake + simple carbs (like a banana) for faster recovery.\r\n\r\nAdjust portion sizes if weight is not moving as desired.', 102, '2026-03-09 19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `exercise`
--

CREATE TABLE `exercise` (
  `id` int(11) NOT NULL,
  `exercise_name` varchar(55) NOT NULL,
  `sets` int(11) NOT NULL,
  `reps` int(11) NOT NULL,
  `rest` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercise`
--

INSERT INTO `exercise` (`id`, `exercise_name`, `sets`, `reps`, `rest`, `trainer_id`, `plan_id`, `created_at`) VALUES
(16, 'Barbell Squat', 4, 6, 120, 111, 5, '2026-03-05 07:30:21'),
(17, 'Bench Press', 4, 8, 120, 111, 5, '2026-03-05 07:30:21'),
(18, 'Deadlift', 4, 5, 150, 111, 5, '2026-03-05 07:30:21'),
(19, 'Pull Ups', 3, 10, 90, 111, 5, '2026-03-05 07:30:21'),
(20, 'Overhead Press', 3, 8, 90, 111, 5, '2026-03-05 07:30:21'),
(21, 'Barbell Row', 3, 10, 90, 111, 5, '2026-03-05 07:30:21'),
(22, 'Leg Press', 3, 12, 90, 111, 5, '2026-03-05 07:30:21'),
(23, 'Plank', 3, 45, 60, 111, 5, '2026-03-05 07:30:21'),
(24, 'Treadmill Running', 4, 5, 60, 111, 6, '2026-03-05 07:32:25'),
(25, 'Jump Rope', 4, 2, 45, 111, 6, '2026-03-05 07:32:25'),
(26, 'Cycling', 3, 10, 60, 111, 6, '2026-03-05 07:32:25'),
(27, 'Mountain Climbers', 3, 30, 45, 111, 6, '2026-03-05 07:32:25'),
(28, 'Burpees', 3, 15, 60, 111, 6, '2026-03-05 07:32:25'),
(29, 'High Knees', 3, 40, 45, 111, 6, '2026-03-05 07:32:25'),
(30, 'Dumbbell Squat', 4, 10, 90, 111, 7, '2026-03-05 07:34:39'),
(31, 'Push Ups', 4, 15, 60, 111, 7, '2026-03-05 07:34:39'),
(32, 'Kettlebell Swings', 3, 20, 60, 111, 7, '2026-03-05 07:34:39'),
(33, 'Lat Pulldown', 3, 12, 90, 111, 7, '2026-03-05 07:34:39'),
(34, 'Battle Ropes', 3, 30, 60, 111, 7, '2026-03-05 07:34:39'),
(35, 'Walking Lunges', 3, 12, 90, 111, 7, '2026-03-05 07:34:39'),
(36, 'Russian Twists', 3, 20, 60, 111, 7, '2026-03-05 07:34:39'),
(37, 'Jump Squats', 3, 15, 60, 111, 7, '2026-03-05 07:34:39'),
(38, 'Resistance Band Squat', 3, 12, 60, 111, 8, '2026-03-05 07:36:18'),
(39, 'Wall Push Ups', 3, 15, 60, 111, 8, '2026-03-05 07:36:18'),
(40, 'Glute Bridge', 3, 15, 60, 111, 8, '2026-03-05 07:36:18'),
(41, 'Bird Dog', 3, 12, 60, 111, 8, '2026-03-05 07:36:18'),
(42, 'Step Ups', 3, 10, 60, 111, 8, '2026-03-05 07:36:18'),
(43, 'Seated Leg Raise', 3, 12, 60, 111, 8, '2026-03-05 07:36:19'),
(44, 'Barbell Squat', 5, 5, 150, 110, 9, '2026-03-05 07:39:55'),
(45, 'Bench Press', 5, 5, 150, 110, 9, '2026-03-05 07:39:55'),
(46, 'Deadlift', 4, 6, 150, 110, 9, '2026-03-05 07:39:55'),
(47, 'Incline Dumbbell Press', 3, 10, 90, 110, 9, '2026-03-05 07:39:55'),
(48, 'Pull Ups', 4, 8, 90, 110, 9, '2026-03-05 07:39:55'),
(49, 'Dumbbell Shoulder Press', 3, 10, 90, 110, 9, '2026-03-05 07:39:56'),
(50, 'Barbell Curl', 3, 12, 60, 110, 9, '2026-03-05 07:39:56'),
(51, 'Hanging Leg Raise', 3, 15, 60, 110, 9, '2026-03-05 07:39:56'),
(52, 'Jump Rope', 5, 2, 45, 110, 10, '2026-03-05 07:41:34'),
(53, 'Burpees', 4, 20, 60, 110, 10, '2026-03-05 07:41:35'),
(54, 'Treadmill Sprint', 4, 1, 60, 110, 10, '2026-03-05 07:41:35'),
(55, 'Cycling', 3, 12, 60, 110, 10, '2026-03-05 07:41:35'),
(56, 'Mountain Climbers', 4, 30, 45, 110, 10, '2026-03-05 07:41:35'),
(57, 'Barbell Back Squat', 5, 5, 3, 109, 11, '2026-03-10 06:54:40'),
(58, 'Deadlift', 4, 6, 3, 109, 11, '2026-03-10 06:54:40'),
(59, 'Weighted Pull-Ups', 4, 10, 90, 109, 11, '2026-03-10 06:54:40'),
(60, 'Overhead Press', 4, 8, 2, 109, 11, '2026-03-10 06:54:40'),
(61, 'Barbell Bench Press', 5, 5, 3, 109, 11, '2026-03-10 06:54:40'),
(62, 'Bulgarian Split Squat (Dumbbell)', 3, 12, 90, 109, 11, '2026-03-10 06:54:40'),
(63, 'Hanging Leg Raises', 3, 20, 60, 109, 11, '2026-03-10 06:54:40'),
(64, 'Farmer’s Walk (Heavy Dumbbells)', 4, 40, 2, 109, 11, '2026-03-10 06:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` int(55) NOT NULL,
  `month` int(12) NOT NULL,
  `year` int(55) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `member_amount` int(55) DEFAULT NULL,
  `trainer_amount` int(255) DEFAULT NULL,
  `payment_status` varchar(55) DEFAULT 'received',
  `month` varchar(55) NOT NULL,
  `year` varchar(55) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `member_id`, `member_amount`, `trainer_amount`, `payment_status`, `month`, `year`, `created_at`) VALUES
(28, 100, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(29, 101, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(30, 102, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(31, 103, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(32, 104, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(33, 105, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(34, 106, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(35, 107, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(36, 108, 3000, 0, 'received', '03', '26', '2026-03-04 19:00:00'),
(37, 109, 0, 45000, 'received', '03', '26', '2026-03-04 19:00:00'),
(38, 110, 0, 50000, 'received', '03', '26', '2026-03-04 19:00:00'),
(39, 111, 0, 42000, 'received', '03', '26', '2026-03-04 19:00:00'),
(40, 112, 0, 55000, 'received', '03', '26', '2026-03-04 19:00:00'),
(41, 113, 0, 48000, 'received', '03', '26', '2026-03-04 19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `plan`
--

CREATE TABLE `plan` (
  `id` int(11) NOT NULL,
  `plan_name` varchar(55) NOT NULL,
  `category` enum('Strength','Cardio','Hybrid','Rehab') NOT NULL,
  `days` varchar(255) NOT NULL,
  `plan_status` int(11) NOT NULL DEFAULT 1,
  `duration` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plan`
--

INSERT INTO `plan` (`id`, `plan_name`, `category`, `days`, `plan_status`, `duration`, `trainer_id`, `created_at`) VALUES
(5, 'Power Builder 12W', 'Strength', 'Mon,Wed,Fri,Sat', 1, 12, 111, '2026-03-05 07:30:21'),
(6, 'Fat Burn Cardio 8W', 'Cardio', 'Mon,Tue,Thu,Fri,Sat', 1, 8, 111, '2026-03-05 07:32:25'),
(7, 'Lean Muscle Hybrid 10W', 'Hybrid', 'Mon,Wed,Thu,Sat', 1, 10, 111, '2026-03-05 07:34:39'),
(8, 'Recovery Mobility 6W', 'Rehab', 'Mon,Wed,Fri', 1, 6, 111, '2026-03-05 07:36:18'),
(9, 'Advanced Strength 10W', 'Strength', 'Mon,Tue,Thu,Fri', 1, 10, 110, '2026-03-05 07:39:55'),
(10, 'Extreme Fat Burner 6W', 'Cardio', 'Mon,Tue,Wed,Fri,Sat', 1, 6, 110, '2026-03-05 07:41:34'),
(11, 'Titan Strength 8W', 'Strength', 'Mon,Tue,Thu,Sat', 1, 8, 109, '2026-03-10 06:54:40');

-- --------------------------------------------------------

--
-- Table structure for table `plan_clients`
--

CREATE TABLE `plan_clients` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plan_clients`
--

INSERT INTO `plan_clients` (`id`, `plan_id`, `client_id`, `assigned_at`) VALUES
(1, 6, 106, '2026-03-05 12:36:28'),
(2, 6, 107, '2026-03-05 12:36:29'),
(3, 7, 105, '2026-03-05 12:36:41'),
(4, 7, 106, '2026-03-05 12:36:42'),
(5, 7, 107, '2026-03-05 12:36:42'),
(6, 11, 101, '2026-03-10 11:55:21'),
(7, 11, 102, '2026-03-10 11:55:21'),
(8, 11, 103, '2026-03-10 11:59:28'),
(9, 11, 108, '2026-03-10 11:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'member'),
(3, 'trainer');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `status` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `status`) VALUES
(1, 'active'),
(2, 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(55) NOT NULL,
  `username` varchar(55) NOT NULL,
  `email` varchar(55) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `role` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `trainer_id` int(11) DEFAULT NULL,
  `fee` int(255) DEFAULT NULL,
  `trainerPay` int(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `pwd`, `role`, `status`, `trainer_id`, `fee`, `trainerPay`, `created_at`) VALUES
(1, 'admin', 'admin1234', 'samad@gmail.com', '$2y$10$ekRFZhbyt8tDAy7ECr/uZe7.B/7TfQzDxl8YlHN.55R58ORp0PtW2', 1, 1, NULL, NULL, NULL, '2026-02-22 14:00:00'),
(101, 'Ali Raza', 'aliraza', 'ali@gmail.com', '$2y$10$DHs.r58LPzl4wnFwRr.I5u.IX3LlAjFPJnWR6kDhoPmguOb99G6d6', 2, 1, 109, 3000, 0, '2026-03-04 19:00:00'),
(102, 'Hassan Malik', 'hassanmalik', 'hassan@gmail.com', '$2y$10$0FNvCLIWsUNcvCkTa9uf8eHDd7.CiUtvo7Bft6yu4KVxrKk8qItzy', 2, 1, 109, 3000, 0, '2026-03-04 19:00:00'),
(103, 'Usman Tariq', 'usmantariq', 'usman@gmail.com', '$2y$10$yVHLUfKNGiaGSBh9jjn.Ge2IPH4xKQsuv6Z/3fFPQiUkn/wNV396S', 2, 1, 109, 3000, 0, '2026-03-04 19:00:00'),
(104, 'Bilal Ahmed', 'bilalahmed', 'bilal@gmail.com', '$2y$10$MdwXaSBBC6InR78IA0rpqO1peXIjWE1FmHw2ENgcanEmTLHKCingG', 2, 1, 113, 3000, 0, '2026-03-04 19:00:00'),
(105, 'Zain Ali', 'zainali', 'zain@gmail.com', '$2y$10$g6DyyFfDm6aX.3cWqetc5eXu7hbn7oHA6176ngGv.dmXXZ/ELv0Yi', 2, 1, 111, 3000, 0, '2026-03-04 19:00:00'),
(106, 'Hamza Khan', 'hamzakhan', 'hamza@gmail.com', '$2y$10$8qeQjjILCPJ8kMFGoh7YtOXoogfZGm.nOpKKaKazqXgZANEw5.xPO', 2, 1, 111, 3000, 0, '2026-03-04 19:00:00'),
(107, 'Saad Hussain', 'saadhussain', 'saad@gmail.com', '$2y$10$.IIKzH8bu1UFfg97B8QZHe7Pcoui4kGkma8tDV49eXkoUJDBcdw72', 2, 1, 110, 3000, 0, '2026-03-04 19:00:00'),
(108, 'Daniyal Shah', 'daniyalshah', 'daniyal@gmail.com', '$2y$10$jJkJQXok3Giyuc9wf21Z5u/RubzqrmiynoDbK5tH4iDLXk56RPSbW', 2, 1, 109, 3000, 0, '2026-03-04 19:00:00'),
(109, 'Umar Farooq', 'umarfarooq', 'umarfarooq@example.com', '$2y$10$WPkqTH/Hp.XMCfRsBY/rDudg0qRcru2M7VEDg0NLD6z1eHEBnOs6q', 3, 1, NULL, 0, 45000, '2026-03-04 19:00:00'),
(110, 'Salman Haider', 'salmanhaider', 'salmanhaider@example.com', '$2y$10$KFMPxgGb5kZSpAg63Q3Q4.Fmt5/onk.T5w1v67t14fnEna6Bot3Ni', 3, 1, NULL, 0, 50000, '2026-03-04 19:00:00'),
(111, 'Kashif Iqbal', 'kashifiqbal', 'kashifiqbal@example.com', '$2y$10$bVWkn3oJPT8MUcXcIxW1P.ahFOXz11N67fXSIywy/kvIkNZeLP1WC', 3, 1, NULL, 0, 42000, '2026-03-04 19:00:00'),
(112, 'Fahad Malik', 'fahadmalik', 'fahadmalik@example.com', '$2y$10$VkZYr59beX2j5aR0FkEZ6OGJJb2VdwDNHZqOCY9N0sP.aAe5MY39y', 3, 1, NULL, 0, 55000, '2026-03-04 19:00:00'),
(113, 'Hamid Raza', 'hamidraza', 'hamidraza@example.com', '$2y$10$nVbujSoUFc3wJ.EmRRn/7.H0r6MuD/d7EZql9WaF8759QBYlquPpG', 3, 1, NULL, 0, 48000, '2026-03-04 19:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diet`
--
ALTER TABLE `diet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exercise`
--
ALTER TABLE `exercise`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plan`
--
ALTER TABLE `plan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plan_clients`
--
ALTER TABLE `plan_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `diet`
--
ALTER TABLE `diet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exercise`
--
ALTER TABLE `exercise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `plan`
--
ALTER TABLE `plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `plan_clients`
--
ALTER TABLE `plan_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
