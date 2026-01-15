-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql310.infinityfree.com
-- Generation Time: Dec 21, 2025 at 02:50 AM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40445601_my_portfolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$TeJNv2erMsVRiq9gR1eRx.SGQGyZ3OzwjToOOYTrPopFX8U4OZGPm');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(2, 'sharath', 'sharatnsharu@gmail.com', 'python', 'sedfsfsv', '2025-11-17 09:55:56'),
(8, 'sharath kumar n', 'gowdruhuduga9972@gmail.com', 'python', 'Sharashfkfajn', '2025-11-17 15:31:48'),
(9, 'Punith', 'srisaipunith2004@gmail.com', 'Job sicker', 'Hi\r\nHow are you', '2025-11-18 11:14:56'),
(10, 'Sharath Kumar N', 'rakesh@gmail.com', 'kannada', 'nithin N', '2025-11-25 08:21:06'),
(11, 'sharath Kumar N', 'sharatnsharu@gmail.com', 'codeing', 'hi', '2025-12-10 07:06:23'),
(12, 'Manali Adventure Trip', 'gowdruhuduga9972@gmail.com', 'codeing', 'lkhnkj', '2025-12-10 07:07:39');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `tech_stack` varchar(200) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `live_url` varchar(255) DEFAULT NULL,
  `project_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `tech_stack`, `image_path`, `github_url`, `live_url`, `project_link`) VALUES
(2, 'Tourism Management System', 'Tour package booking, user login, admin panel, and image gallery.', 'PHP, MySQL, HTML, CSS', 'project_images/project_20251209_055957_3328.png', '', 'https://travelindia-dev-sharath.kesug.com', '#'),
(10, 'Bank Customer Segmentation Using Clustering', 'A data science project that segments bank customers based on demographic and financial behavior using unsupervised clustering. Applied K-Means algorithm and PCA for dimensionality reduction to identify customer groups for targeted marketing strategies.', 'Python, Pandas, NumPy, Scikit-Learn, Matplotlib, Seaborn, Jupyter Notebook, PCA, K-Means Clustering', 'project_images/project_20251205_090118_3327.png', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `site_content`
--

CREATE TABLE `site_content` (
  `id` int(11) NOT NULL,
  `content_key` varchar(100) NOT NULL,
  `content_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_content`
--

INSERT INTO `site_content` (`id`, `content_key`, `content_value`) VALUES
(1, 'hero_title', 'Sharath Kumar N'),
(2, 'hero_subtitle', 'Aspiring Data Scientist & Web Developer'),
(3, 'hero_description', 'I build data-driven solutions and modern web applications using Python, PHP, MySQL, and JavaScript.'),
(4, 'hero_button1_text', 'View Projects'),
(5, 'hero_button1_link', 'projects.php'),
(6, 'hero_button2_text', 'Contact Me'),
(7, 'hero_button2_link', 'contact.php'),
(8, 'about_p1', 'I am Sharath Kumar N, passionate about data analysis, machine learning, and front-end development. I enjoy working with Python, SQL, PHP, and building real-world projects using XAMPP and MySQL. I love transforming raw data into meaningful insights and creating clean, responsive, and user-friendly interfaces. I constantly explore new front-end tools, design practices, and modern UI technologies to improve my skills. Driven by curiosity and continuous learning, I focus on turning ideas into visually appealing, efficient, and intuitive applications that provide a great user experience.'),
(9, 'about_p2', 'I love learning new tools and applying them to solve practical problems, especially in data science and web development. I enjoy experimenting with new technologies and improving my skill set every day. Iâ€™m always eager to explore innovative approaches that make solutions more efficient and impactful. I believe in continuous growth and enjoy taking on challenges that push my creativity and technical abilities. Iâ€™m passionate about building projects that combine logic, design, and real-world usefulness, and I constantly look for opportunities to expand my knowledge and apply it to meaningful work.'),
(10, 'about_education', 'BCA /IN SAHYADRI DEGREE COLLEGE KOLAR,563101'),
(11, 'about_interests', 'Data Science â†’ Data Science & Advanced Analytics  Web Development â†’ Front-End & Web Application Development  Analytics â†’ Data Analytics & Insight Generation  Problem Solving â†’ Strategic Problem-Solving & Technical Decision-Making'),
(12, 'resume_text', 'You can download my latest resume using the button below. It includes my education, skills, projects, and experience.'),
(13, 'resume_file', 'resume/resume_20251120_063932.pdf'),
(14, 'contact_email', 'sharatnsharu@gmail.com'),
(15, 'contact_location', 'Bengaluru, Karnataka, India'),
(16, 'contact_linkedin', 'www.linkedin.com/in/sharath-kumar-n-9b3662313'),
(17, 'contact_github', '-----------------------'),
(86, 'profile_image', 'images/profile_20251117_132253.jpeg'),
(204, 'about_education1', 'BCA  |  SAHYADRI DEGREE COLLEGE, KOLAR  |  CGPA:9.07  |  2025'),
(205, 'about_education2', 'PUC  |  MAHILA SAMAJA PU COLLEGE, KOLAR  |  91%  |  2022'),
(206, 'about_education3', 'SSLC  |  KG INTERNATIONL SCHOOL, KOLAR  |  79%  |  2020');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `items` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `category`, `items`) VALUES
(73, 'Programming', 'Python (NumPy Pandas), PHP, JavaScript (basic)'),
(74, 'Data & ML', 'Statistics & Data Analysis, Scikit-learn (basic ML), Excel, Power BI (basics)'),
(75, 'Web & DB', 'HTML, CSS, MySQL, XAMPP, Basic REST APIs');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_content`
--
ALTER TABLE `site_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `content_key` (`content_key`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `site_content`
--
ALTER TABLE `site_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=461;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
