-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Dec 06, 2023 at 03:24 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project-iir`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `citations` int(11) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `abstract` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `citations`, `author`, `abstract`) VALUES
(1, 'Review on Convolutional Neural Networks (CNN) in vegetation remote sensing', 701, 'Teja Kattenborn, Jens Leitloff, Felix Schiefer, Stefan Hinz', 'Identifying and characterizing vascular plants in time and space is required in various disciplines, e.g. in forestry, conservation and agriculture. Remote sensing emerged as a key technology revealing both spatial and temporal vegetation patterns. Harnes'),
(2, 'Review of deep learning: Concepts, CNN architectures, challenges, applications, future directions', 2881, 'Laith Alzubaidi, Jinglan Zhang, Amjad J Humaidi, Ayad Al-Dujaili, Ye Duan, Omran Al-Shamma, J Santamar?a, Mohammed A Fadhel, Muthana Al-Amidie, Laith Farhan', 'In the last few years, the deep learning (DL) computing paradigm has been deemed the Gold Standard in the machine learning (ML) community. Moreover, it has gradually become the most widely used computational approach in the field of ML, thus achieving out'),
(3, 'Universal CNN cells', 130, 'Radu Dogaru, Leon O Chua', 'A cellular neural/nonlinear network (CNN) [Chua, 1998] is a biologically inspired system where computation emerges from a collection of simple <i>nonlinear</i> locally coupled cells. This paper reviews our recent research results beginning from the standa'),
(4, 'Deep convolutional neural networks for computer-aided detection: CNN architectures, dataset characteristics and transfer learning', 5422, 'Hoo-Chang Shin, Holger R Roth, Mingchen Gao, Le Lu, Ziyue Xu, Isabella Nogues, Jianhua Yao, Daniel Mollura, Ronald M Summers', 'Remarkable progress has been made in image recognition, primarily due to the availability of large-scale annotated datasets and deep convolutional neural networks (CNNs). CNNs enable learning data-driven, highly representative, hierarchical image features'),
(5, 'Deep learning techniques&#8212;R-CNN to mask R-CNN: a survey', 202, 'Puja Bharati, Ankita Pramanik', 'With the advances in the field of machine learning, statistics, and computer vision, the advanced deep learning techniques have attracted increasing research interests over the last decade. This is because of their inherent capabilities of overcoming the '),
(6, 'CNN variants for computer vision: History, architecture, application, challenges and future scope', 265, 'Dulari Bhatt, Chirag Patel, Hardik Talsania, Jigar Patel, Rasmika Vaghela, Sharnil Pandya, Kirit Modi, Hemant Ghayvat', 'Computer vision is becoming an increasingly trendy word in the area of image processing. With the emergence of computer vision applications, there is a significant demand to recognize objects automatically. Deep CNN (convolution neural network) has benefi'),
(7, 'A survey of recent advances in cnn-based single image crowd counting and density estimation', 570, 'Vishwanath A Sindagi, Vishal M Patel', 'Estimating count and density maps from crowd images has a wide range of applications such as video surveillance, traffic monitoring, public safety and urban planning. In addition, techniques developed for crowd counting can be applied to related tasks in '),
(8, 'Analysis of dentigerous cyst, ameloblastoma, and odontogenic keratocyst panoramic radiograph and CBCT: a scoping review', 1, 'Monica Siregar, Suhardjo Sitam, Yurika Ambar Lita, Indra Hadikrishna', 'Background: The radiographic images similarity of a dentigerous cyst, ameloblastoma, and odontogenic keratocyst can lead to misdiagnosis. The radiographic images of these lesions can be analyzed using panoramic radiographs and CBCT with quantitative and q');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
