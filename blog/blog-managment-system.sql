-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2026 at 07:32 AM
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
-- Database: `blog-managment-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Technology', 'technology', 'Tech related posts'),
(2, 'Business', 'business', 'Business news & tips'),
(3, 'Lifestyle', 'lifestyle', 'Life & style content');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `author_email` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','spam','trash') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `user_id`, `file_path`, `file_name`, `mime_type`, `uploaded_at`) VALUES
(1, 1, 'assets/uploads/1768369613_69672dcd5f162.jpg', 'Quotefancy-8151357-3840x2160.jpg', 'image/jpeg', '2026-01-14 11:16:53');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `status` enum('draft','published','archived','trash') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `published_at` datetime DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `featured_image_alt` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `index_status` enum('index','noindex') DEFAULT 'index',
  `schema_type` varchar(50) DEFAULT 'BlogPosting',
  `schema_organization` varchar(255) DEFAULT NULL,
  `schema_logo` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `slug`, `canonical_url`, `content`, `excerpt`, `status`, `created_at`, `updated_at`, `published_at`, `featured_image`, `featured_image_alt`, `meta_title`, `meta_keywords`, `meta_description`, `index_status`, `schema_type`, `schema_organization`, `schema_logo`) VALUES
(7, 1, 'dfadfd fdfdf yuyuyu', 'dfadfd-fdfdf-yuyuyu', '', '<p data-start=\"394\" data-end=\"698\">This post does not contain any meaningful or final information. Instead, it serves as a starting point for writers, developers, and designers who are setting up a new blog. The &ldquo;Hello World&rdquo; post is often the first step in publishing content, symbolizing the beginning of a creative or technical journey.</p>\r\n<p data-start=\"700\" data-end=\"1020\">You can replace this dummy text with real content once your ideas, research, and writing are ready. Until then, this text helps visualize how long paragraphs, headings, and formatting will appear on the page. It is commonly used during development, testing, or learning phases to ensure everything functions as expected.</p>\r\n<p data-start=\"1022\" data-end=\"1180\">In short, this &ldquo;Hello World&rdquo; article is simply a placeholder, waiting to be transformed into something meaningful, informative, and engaging for real readers.</p>\r\n<p data-start=\"1022\" data-end=\"1180\">&nbsp;</p>\r\n<p data-start=\"394\" data-end=\"698\">This post does not contain any meaningful or final information. Instead, it serves as a starting point for writers, developers, and designers who are setting up a new blog. The &ldquo;Hello World&rdquo; post is often the first step in publishing content, symbolizing the beginning of a creative or technical journey.</p>\r\n<p data-start=\"700\" data-end=\"1020\">You can replace this dummy text with real content once your ideas, research, and writing are ready. Until then, this text helps visualize how long paragraphs, headings, and formatting will appear on the page. It is commonly used during development, testing, or learning phases to ensure everything functions as expected.</p>\r\n<p data-start=\"1022\" data-end=\"1180\">In short, this &ldquo;Hello World&rdquo; article is simply a placeholder, waiting to be transformed into something meaningful, informative, and engaging for real readers.</p>', NULL, 'published', '2026-01-14 11:58:49', '2026-01-14 11:58:49', '2026-01-14 07:28:49', 'assets/uploads/posts/1768372129_Quotefancy-8151357-3840x2160.jpg', 'ddfdf', '', '', '', 'index', 'BlogPosting', '', ''),
(8, 1, 'dfsfdfdsfsd ffdfdfs', 'dfsfdfdsfsd-ffdfdfs', '', '<p data-start=\"394\" data-end=\"698\">This post does not contain any meaningful or final information. Instead, it serves as a starting point for writers, developers, and designers who are setting up a new blog. The &ldquo;Hello World&rdquo; post is often the first step in publishing content, symbolizing the beginning of a creative or technical journey.</p>\r\n<p data-start=\"700\" data-end=\"1020\">You can replace this dummy text with real content once your ideas, research, and writing are ready. Until then, this text helps visualize how long paragraphs, headings, and formatting will appear on the page. It is commonly used during development, testing, or learning phases to ensure everything functions as expected.</p>\r\n<p data-start=\"1022\" data-end=\"1180\">In short, this &ldquo;Hello World&rdquo; article is simply a placeholder, waiting to be transformed into something meaningful, informative, and engaging for real readers.</p>', NULL, 'published', '2026-01-14 11:59:35', '2026-01-14 11:59:59', '2026-01-14 07:29:35', 'assets/uploads/posts/1768372199_Quotefancy-8151357-3840x2160.jpg', 'dfdadf', '', '', '', 'index', 'BlogPosting', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `post_categories`
--

CREATE TABLE `post_categories` (
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE `post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`) VALUES
(1, 'PHP', 'php'),
(2, 'MySQL', 'mysql'),
(3, 'Web Development', 'web-development'),
(4, 'AI', 'ai');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','author','editor') DEFAULT 'author',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `display_name`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2a$12$WxIOXdILjrXDnNKoxneALOVzpPC7ccbcEaHzi2M8ZE.blCqqGoVL.', 'Administrator', 'admin', '2026-01-13 12:11:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD CONSTRAINT `post_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
