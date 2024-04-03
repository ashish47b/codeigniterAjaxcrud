-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2024 at 04:41 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `itmelist`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `final_submit` int(2) DEFAULT 0 COMMENT '0 is clone and 1 is final submit',
  `created` datetime DEFAULT current_timestamp(),
  `updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `price`, `image_url`, `final_submit`, `created`, `updated`) VALUES
(1, '[\"Iphone\",\"Nokia\",\"Samsung\"]', '[\"The iPhone is a smartphone made by Apple that combines a computer, iPod, digital camera, and cellular phone into one device. It has a multi-touch display, built-in sensors, and external controls. The iPhone is known for its touch screen that allows quick response to single or multiple finger strokes. It runs on Apple\'s proprietary iOS operating system, which is designed for use with Apple\'s multitouch devices\",\"Nokia is a Finnish multinational corporation that specializes in telecommunications, information technology, and consumer electronics. It was founded in 1865 as a paper mill, and has since expanded into many other products. Nokia was a leader in the mobile market in the early days of smartphones, and played a key role in developing GSM (Global System for Mobile Communications) in the late 1980s and early 1990s. \\r\\n\\r\\nWikipedia\\r\\nNokia - Wikipedia\\r\\nNokia Corporation (natively Nokia Oyj in Finnish and Nokia Abp in Swedish, referred to as Nokia) is a Finnish multinational telecommunications, information technology, and consumer electronics corporation, established in 1865. ... The company has operated in various industries over the past 150 years. ... After a partnership with Microsoft and Nokia\'s subsequent market struggles, in 2014 Microsoft bought Nokia\'s mobile phone business, incorporating it as Microsoft Mobile. ... The company was viewed with national pride by Finns, as its mobile phone business made it by far the largest worldwide company and brand from Finland.\\r\\n\\r\\nStatista\\r\\nNokia - statistics & facts | Statista\\r\\n\\r\\nWikipedia\\r\\nHistory of Nokia - Wikipedia\\r\\nNokia is a Finnish multinational corporation founded on 12 May 1865 as a single paper mill operation. Through the 19th century the company expanded, branching into several different products. In 1967, the Nokia corporation was formed. In the late 20th century, the company took advantage of the increasing popularity of computer and mobile phones. However, increased competition and other market forces caused changes in Nokia\'s business arrangements. In 2014, Nokia\'s mobile phone business was sold to Microsoft.\\r\\n\\r\\nBrainly.in\\r\\nwhat is meaning of nokia\\u200b - Brainly.in\\r\\n26 \\u091c\\u0928\\u0970 2022 \\u2014 Nokia Corporation is a Finnish multinational telecommunications, information technology, and consumer electronics company, founded in 1865. Nokia\'s main headquarters are in Espoo, Finland, in the greater Helsinki metropolitan area, but the company\'s actual roots are in the Tampere region of Pirkanmaa.\\r\\nNokia\'s main headquarters are in Espoo, Finland, but the company\'s roots are in the Tampere region of Pirkanmaa. Nokia has operated in various industries over the past 150 years, including research into the first long-distance television transmission in the US in 1927, and the creation of the first full-length motion picture with synchronized sound in 1925. \\r\\nIn 2014, Microsoft bought Nokia\'s mobile phone business, incorporating it as Microsoft Mobile. Today, Nokia makes money through selling networking equipment, licensing its patents, and pushing hard into 5G. Nokia-branded phones are still sold by HMD Global.\",\"Samsung is a global leader in technology, opening new possibilities for people everywhere. Through relentless innovation and discovery, they are transforming the worlds of TVs, smartphones, wearable devices, tablets, digital appliances, network systems, medical devices, semiconductors, and LED solutions.\"]', '[\"4500\",\"1500\",\"4500\"]', '[\"download_1712108860.jpg\",\"download (1)_1712108860.jpg\",\"samsung_galaxyS22-f_mobile_1712108860.jpg\"]', 1, '2024-04-03 07:17:40', '2024-04-03 07:45:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
