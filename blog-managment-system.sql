-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2026 at 11:48 AM
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
(2, 1, 'assets/uploads/1775289031_69d0c2c77c707.jpeg', 'detox-support.jpeg', 'image/jpeg', '2026-04-04 13:20:31');

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
(28, 1, 'What is a Blog? A Complete Guide for Beginners', 'what-is-a-blog-a-complete-guide-for-beginners', '', '<p>In today&rsquo;s digital world, the word &ldquo;blog&rdquo; is everywhere. Whether you&rsquo;re searching for information, reading personal stories, or learning a new skill, chances are you&rsquo;ve come across a blog. But what exactly is a blog, and why has it become such an important part of the internet?</p>\r\n<h2>Definition of a Blog</h2>\r\n<p>A blog is a type of website or an online platform where individuals or groups regularly publish written content, known as posts or articles. These posts are usually displayed in reverse chronological order, meaning the newest content appears first.</p>\r\n<p><img src=\"http://localhost/blog-management-system/assets/uploads/tinymce/1775282491_69d0a93bb9770.webp\" alt=\"imgblog\" width=\"1280\" height=\"720\"></p>\r\n<p class=\"isSelectedEnd\">Blogs can be about anything&mdash;personal experiences, travel, technology, education, food, fashion, business, or even hobbies. The flexibility of blogging is what makes it so popular.</p>\r\n<p class=\"isSelectedEnd\">For example, some bloggers use their platforms to share personal life stories, daily routines, or motivational experiences that inspire others. Travel bloggers document their journeys, explore new destinations, and provide tips, guides, and recommendations for fellow travelers. Technology blogs focus on the latest gadgets, software updates, tutorials, and industry trends, helping readers stay informed in a fast-changing digital world.</p>\r\n<h2>History of Blogging</h2>\r\n<p>Blogging started in the late 1990s as a form of online diary. People used blogs to share their thoughts, daily activities, and personal experiences. Over time, blogs evolved into powerful tools for communication, marketing, and education.</p>\r\n<p>Today, blogs are used by individuals, companies, and organizations to connect with audiences, share knowledge, and even earn money.</p>\r\n<h2>Types of Blogs</h2>\r\n<p>There are many different types of blogs, depending on the purpose and audience:</p>\r\n<h3>1. Personal Blogs</h3>\r\n<p>These are like online journals where people share their personal thoughts, stories, and experiences.</p>\r\n<h3>2. Professional Blogs</h3>\r\n<p>These focus on a specific topic such as business, technology, health, or education, and aim to provide useful information to readers.</p>\r\n<h3>3. Niche Blogs</h3>\r\n<p>These blogs target a specific audience or topic, such as fitness, travel, food recipes, or digital marketing.</p>\r\n<h3>4. Business Blogs</h3>\r\n<p>Companies use blogs to promote their products or services, build brand awareness, and engage with customers.</p>\r\n<h2><img src=\"http://localhost/blog-management-system/assets/uploads/tinymce/1775282536_69d0a96896d7c.webp\" alt=\"blogimg2\" width=\"640\" height=\"361\"></h2>\r\n<h2>Key Features of a Blog</h2>\r\n<p>A blog typically includes the following features:</p>\r\n<p><img src=\"http://localhost/blog-management-system/assets/uploads/tinymce/1775285928_69d0b6a85d54c.jfif\" alt=\"dssd\" width=\"275\" height=\"183\"></p>\r\n<ul>\r\n<li data-section-id=\"4p51ws\" data-start=\"0\" data-end=\"52\"><strong data-start=\"2\" data-end=\"20\" data-is-only-node=\"\">Posts/Articles</strong>: The main content of the blog</li>\r\n<li data-section-id=\"1idalrf\" data-start=\"53\" data-end=\"124\"><strong data-start=\"55\" data-end=\"75\" data-is-only-node=\"\">Comments Section</strong>: Allows readers to interact and share opinions</li>\r\n<li data-section-id=\"6e552\" data-start=\"125\" data-end=\"176\"><strong data-start=\"127\" data-end=\"150\" data-is-only-node=\"\">Categories and Tags</strong>: Helps organize content</li>\r\n<li data-section-id=\"10kk5o2\" data-start=\"177\" data-end=\"229\"><strong data-start=\"179\" data-end=\"201\" data-is-only-node=\"\">Author Information</strong>: Details about the writer</li>\r\n<li data-section-id=\"wn802r\" data-start=\"230\" data-end=\"283\"><strong data-start=\"232\" data-end=\"251\" data-is-only-node=\"\">Regular Updates</strong>: Blogs are updated frequently</li>\r\n<li data-section-id=\"1ith5sb\" data-start=\"284\" data-end=\"355\"><strong data-start=\"286\" data-end=\"310\" data-is-only-node=\"\">Search Functionality</strong>: Helps users quickly find specific content</li>\r\n<li data-section-id=\"h0n78l\" data-start=\"356\" data-end=\"442\"><strong data-start=\"358\" data-end=\"380\" data-is-only-node=\"\">Multimedia Content</strong>: Includes images, videos, and infographics to enhance posts</li>\r\n<li data-section-id=\"otr1cc\" data-start=\"443\" data-end=\"522\"><strong data-start=\"445\" data-end=\"471\" data-is-only-node=\"\">Social Sharing Options</strong>: Allows readers to share content on social media</li>\r\n<li data-section-id=\"1sjl90i\" data-start=\"523\" data-end=\"601\" data-is-last-node=\"\"><strong data-start=\"525\" data-end=\"549\" data-is-only-node=\"\">User-Friendly Design</strong>: Ensures easy navigation and better user experience</li>\r\n</ul>\r\n<h2>Why Are Blogs Important?</h2>\r\n<p>Blogs play a major role in today&rsquo;s digital ecosystem. Here&rsquo;s why they matter:</p>\r\n<ul>\r\n<li>\r\n<p><strong>Information Sharing:</strong> Blogs provide valuable knowledge on various topics</p>\r\n</li>\r\n<li>\r\n<p><strong>Communication:</strong> They help people express ideas and connect with others</p>\r\n</li>\r\n<li>\r\n<p><strong>SEO and Marketing:</strong> Businesses use blogs to improve their online visibility</p>\r\n</li>\r\n<li>\r\n<p><strong>Earning Opportunities:</strong> Bloggers can earn through ads, sponsorships, and affiliate marketing</p>\r\n</li>\r\n</ul>\r\n<h2>Benefits of Blogging</h2>\r\n<p>Starting a blog can be beneficial in many ways:</p>\r\n<p><img src=\"http://localhost/blog-management-system/assets/uploads/tinymce/1775287728_69d0bdb02145a.jfif\" alt=\"\" width=\"259\" height=\"194\"></p>\r\n<ul>\r\n<li>\r\n<p>Improves writing and communication skills</p>\r\n</li>\r\n<li>\r\n<p>Builds personal or professional brand</p>\r\n</li>\r\n<li>\r\n<p>Helps in networking with like-minded people</p>\r\n</li>\r\n<li>\r\n<p>Creates passive income opportunities</p>\r\n</li>\r\n</ul>\r\n<h2>How to Start a Blog</h2>\r\n<p>If you want to start your own blog, follow these basic steps:</p>\r\n<ol>\r\n<li>\r\n<p>Choose a topic you are passionate about</p>\r\n</li>\r\n<li>\r\n<p>Select a blogging platform (like WordPress or Blogger)</p>\r\n</li>\r\n<li>\r\n<p>Pick a domain name</p>\r\n</li>\r\n<li>\r\n<p>Create and publish quality content</p>\r\n</li>\r\n<li>\r\n<p>Promote your blog through social media</p>\r\n</li>\r\n</ol>\r\n<h2>Conclusion</h2>\r\n<p>A blog is more than just a website&mdash;it&rsquo;s a powerful tool for sharing ideas, building connections, and creating opportunities. Whether you want to express yourself, educate others, or grow a business, blogging offers endless possibilities.</p>\r\n<p>If you are consistent and passionate, blogging can become a rewarding journey both personally and professionally.</p>', NULL, 'published', '2026-04-04 11:32:27', '2026-04-04 12:59:24', '2026-04-04 08:02:27', 'assets/uploads/posts/1775282547_How_To_Start_A_Blog_-_article_image.webp', '', '', '', '', 'index', 'BlogPosting', '', '');

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
(1, 'admin', 'admin@example.com', '$2a$12$WxIOXdILjrXDnNKoxneALOVzpPC7ccbcEaHzi2M8ZE.blCqqGoVL.', 'Administrator', 'admin', '2026-01-13 12:11:48'),
(2, 'ram', 'ram.ssp@gmail.com', '$2y$10$gSLNMjxwfSI7LT/sULPVI.HRrZyfIWUBPAPfiqun.55RyiZz3FHtK', 'Ram', 'author', '2026-04-04 13:21:25');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
