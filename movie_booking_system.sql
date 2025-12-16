-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 01:49 PM
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
-- Database: `movie_booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `seats` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `status` enum('confirmed','cancelled') DEFAULT 'confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `cast` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `genre`, `language`, `duration`, `release_date`, `description`, `poster`, `director`, `cast`) VALUES
(1, 'Demon Slayer: Kimetsu no Yaiba - The Movie: Infinity Castle', 'Adventure, Fantasy, Animation', 'English(Sub)', 150, '2025-07-18', 'The Demon Slayer Corps are drawn into the Infinity Castle, where Tanjiro and the Hashira face terrifying Upper Rank demons in a desperate fight as the final battle against Muzan Kibutsuji begins.', 'demonSlayer.jpg', 'Haruo Sotozaki', 'Natsuki Hanae, Akari Kito, Yoshitsugu Matsuoka'),
(2, 'The Conjuring: Last Rites', 'Horror, Thriller, Mystery', 'English', 135, '2025-09-05', 'Paranormal investigators Ed and Lorraine Warren take on one last terrifying case involving mysterious entities they must confront.', 'conjuring.jpg', 'Michael Chaves', 'Patrick Wilson, Vera Farmiga, Ben Hardy'),
(3, 'Kantara: A Legend Chapter-1', 'Action, Thriller', 'Hindi (Dub)', 169, '2025-10-02', 'In pre-colonial Karnataka, during the Kadamba dynasty era, the ritual of Bhuta Kola takes root in the culture. Meanwhile, the seeds for the rise of Kaadubettu Shiva are also sown.', 'kantara.jpg', 'Rishab Shetty', 'Rishab Shetty, Gulshan Devaiah, Achyuth Kumar'),
(4, 'Soltinee', 'Drama', 'Nepali', 143, '2025-10-23', 'Set amidst the scenic hills of Ilam and Bhojpur, Soltinee is a heartfelt romantic drama that beautifully blends love, tradition, and culture. The story unfolds as passion and destiny intertwine in a family torn between duty and desire. With its mix of emotion, love, and humor, Soltinee promises an unforgettable journey of love that dares to challenge boundaries.', 'soltinee.jpg', 'Arjunn Subedi', 'Prakash Saput, Parikshya Limbu, Wilson Bikram Rai'),
(5, 'Chainsaw Man – The Movie: Reze Arc', 'Animation', 'Japanese (Eng Sub)', 100, '2025-10-24', 'Denji encounters a new romantic interest, Reze, who works at a coffee café.', 'chainsawman.jpg', 'Tatsuya Yoshihara', 'Kikunosuke Toya, Reina Ueda, Shiori Izawa'),
(9, 'Paran', 'Drama', 'Nepali', 146, '2025-10-31', 'Paran is a heartfelt family drama about Dharmanath (Neer Shah), who treasures his children as his “essence of life” and dreams of growing old in their love. Set in Dhankuta, the film beautifully portrays love, legacy, and the true meaning of family.', 'paran.jpg', 'Deepak Prasad Acharya', 'Nir Shah, Madan Krishna Shrestha, Puja Chand Lam,  Keki Adhikari');

-- --------------------------------------------------------

--
-- Table structure for table `showtime`
--

CREATE TABLE `showtime` (
  `showtime_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `showtime`
--

INSERT INTO `showtime` (`showtime_id`, `movie_id`, `show_date`, `show_time`) VALUES
(1, 1, '2025-11-08', '09:00:00'),
(2, 1, '2025-11-09', '14:00:00'),
(6, 1, '2025-11-08', '13:00:00'),
(7, 2, '2025-11-10', '09:00:00'),
(8, 2, '2025-11-10', '13:00:00'),
(9, 2, '2025-11-11', '10:00:00'),
(10, 2, '2025-11-11', '13:00:00'),
(11, 2, '2025-11-12', '09:00:00'),
(12, 3, '2025-11-13', '08:00:00'),
(13, 3, '2025-11-13', '12:00:00'),
(14, 3, '2025-11-14', '11:00:00'),
(15, 4, '2025-11-13', '09:00:00'),
(16, 5, '2025-11-14', '11:00:00'),
(17, 5, '2025-11-14', '19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `mobile_no` varchar(10) DEFAULT NULL,
  `role` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `mobile_no`, `role`) VALUES
(2, 'Samyak Dangol', 'samyakoe@gmail.com', '$2y$10$W5pITyc7913qkCwmeLGTme9DA0sRcsfhXiB7K3nryKt1CL4fx7dc2', '9761720570', 1),
(4, 'Test', 'test@gmail.com', '$2y$10$pZ74pFKCj0mEA2vqHd150eHurYgMMytypXtiy3Lfjgb3IJbu5cARi', '969483', NULL),
(5, 'User', 'usertry@gmail.com', '$2y$10$G3/zs6nFFOxe9RxjD7w6/eSXen/oj5OqUR7pDl8gtLKmmB1ixaj1C', '9885678735', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `showtime_id` (`showtime_id`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `showtime`
--
ALTER TABLE `showtime`
  ADD PRIMARY KEY (`showtime_id`),
  ADD KEY `fk_movie_id` (`movie_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `showtime`
--
ALTER TABLE `showtime`
  MODIFY `showtime_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`showtime_id`) REFERENCES `showtime` (`showtime_id`);

--
-- Constraints for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `showtime`
--
ALTER TABLE `showtime`
  ADD CONSTRAINT `fk_movie_id` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
