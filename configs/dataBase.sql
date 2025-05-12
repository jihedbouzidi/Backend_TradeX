-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 12, 2025 at 07:20 PM
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
-- Database: `TradeX_BD`
--

-- --------------------------------------------------------

--
-- Table structure for table `imagesPub`
--

CREATE TABLE `imagesPub` (
  `id` int(11) NOT NULL,
  `chemin` varchar(255) NOT NULL,
  `publication_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `imagesPub`
--

INSERT INTO `imagesPub` (`id`, `chemin`, `publication_id`) VALUES
(79, 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 76),
(80, 'https://images.unsplash.com/photo-1593642702909-dec73df255d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 76),
(81, 'https://images.unsplash.com/photo-1617625600633-69e3d113087b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 77),
(82, 'https://images.unsplash.com/photo-1610945260566-76b6f6516a74?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 77),
(83, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 77),
(84, 'https://images.unsplash.com/photo-1620584455-0b5f1e4a7f5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 78),
(85, 'https://images.unsplash.com/photo-1593642632556-2f7e7d3f8a3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 78),
(86, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 78),
(87, 'https://images.unsplash.com/photo-1631711339809-04c3e7f3a02d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 78),
(88, 'https://images.unsplash.com/photo-1629367494173-c78f385e0e44?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 79),
(89, 'https://images.unsplash.com/photo-1616348436168-4f4a8f2d2d6f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 80),
(90, 'https://images.unsplash.com/photo-1616628188558-3fa1d7d8a7a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 80),
(91, 'https://images.unsplash.com/photo-1631624215749-6802663a9e82?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 81),
(92, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 81),
(93, 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 82),
(94, 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 83),
(95, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 83),
(96, 'https://images.unsplash.com/photo-1624704795324-5156c6a2e497?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 84),
(97, 'https://images.unsplash.com/photo-1593642634367-d91a5a4f8c67?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 84),
(98, 'https://images.unsplash.com/photo-1610878180933-123728745d22?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 85),
(99, 'https://images.unsplash.com/photo-1593642634315-48f5414c3ad9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 86),
(100, 'https://images.unsplash.com/photo-1610945260566-76b6f6516a74?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 87),
(101, 'https://images.unsplash.com/photo-1620584455-0b5f1e4a7f5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 88),
(102, 'https://images.unsplash.com/photo-1593642632556-2f7e7d3f8a3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 88),
(103, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 88),
(104, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 89),
(105, 'https://images.unsplash.com/photo-1616348436168-4f4a8f2d2d6f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 90),
(106, 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 91),
(107, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 91),
(108, 'https://images.unsplash.com/photo-1624704795324-5156c6a2e497?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 92),
(109, 'https://images.unsplash.com/photo-1617625600633-69e3d113087b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 93),
(110, 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 94),
(111, 'https://images.unsplash.com/photo-1629367494173-c78f385e0e44?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 95),
(112, 'https://images.unsplash.com/photo-1631711339809-04c3e7f3a02d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 95),
(163, '/img4.png', 109);

-- --------------------------------------------------------

--
-- Table structure for table `panier`
--

CREATE TABLE `panier` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `publication_id` int(11) DEFAULT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication`
--

CREATE TABLE `publication` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `type_app` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `date_publication` datetime DEFAULT current_timestamp(),
  `objectif` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`id`, `utilisateur_id`, `type_app`, `description`, `facebook`, `whatsapp`, `date_publication`, `objectif`) VALUES
(76, 133, 'pc', 'PC portable Dell XPS 13, Intel i7-11e gen, 16GB RAM, SSD 512GB, écran 13.4\" 4K. Très bon état, utilisé 1 an.', 'https://www.facebook.com/amira.saidi', 'https://wa.me/27123456', '2025-05-10 01:30:00', 'Vendre ou échanger contre un MacBook Pro M1 avec au moins 16GB RAM.'),
(77, 134, 'mobile', 'Samsung Galaxy S23 Ultra, 256GB, 12GB RAM, caméra 200MP, noir, comme neuf.', 'https://www.facebook.com/karim.trabelsi', 'https://wa.me/29876543', '2025-05-10 01:35:00', 'Échanger contre iPhone 14 Pro Max ou vendre.'),
(78, 135, 'pc', 'PC gaming custom, Ryzen 7 5800X, RTX 3080, 32GB RAM, SSD 1TB NVMe, watercooling.', 'https://www.facebook.com/fatma.benali', 'https://wa.me/22334455', '2025-05-10 01:40:00', 'Recherche PC avec RTX 3090 ou supérieur pour upgrade.'),
(79, 136, 'mobile', 'iPhone 13 Pro, 128GB, bleu sierra, batterie 90%, léger rayures sur l’écran.', 'https://www.facebook.com/omar.gharbi', 'https://wa.me/26789012', '2025-05-10 01:45:00', 'Vendre ou échanger contre Google Pixel 8 Pro.'),
(80, 137, 'pc', 'MacBook Air M2, 8GB RAM, 256GB SSD, écran 13.6\", couleur minuit, neuf.', 'https://www.facebook.com/sana.mhamdi', 'https://wa.me/25432109', '2025-05-10 01:50:00', 'Échanger contre MacBook Pro 14\" avec 16GB RAM.'),
(81, 133, 'mobile', 'Google Pixel 7, 128GB, 8GB RAM, blanc, excellent état, avec chargeur.', 'https://www.facebook.com/amira.saidi', 'https://wa.me/27123456', '2025-05-10 01:55:00', 'Recherche Samsung Galaxy S24 ou iPhone 15.'),
(82, 134, 'pc', 'PC de bureau HP, Intel i5-10e gen, 16GB RAM, HDD 1TB + SSD 256GB.', 'https://www.facebook.com/karim.trabelsi', 'https://wa.me/29876543', '2025-05-10 02:00:00', 'Vendre ou échanger contre PC portable gaming.'),
(83, 135, 'mobile', 'Xiaomi 13 Pro, 256GB, 12GB RAM, caméra Leica, noir céramique.', 'https://www.facebook.com/fatma.benali', 'https://wa.me/22334455', '2025-05-10 02:05:00', 'Échanger contre OnePlus 12 ou vendre.'),
(84, 136, 'pc', 'Laptop ASUS ROG Zephyrus, Ryzen 9 5900HS, RTX 3070, 16GB RAM, 1TB SSD.', 'https://www.facebook.com/omar.gharbi', 'https://wa.me/26789012', '2025-05-10 02:10:00', 'Recherche PC avec RTX 3080 ou supérieur.'),
(85, 137, 'mobile', 'OnePlus 11, 128GB, 8GB RAM, vert, très bon état.', 'https://www.facebook.com/sana.mhamdi', 'https://wa.me/25432109', '2025-05-10 02:15:00', 'Vendre ou échanger contre iPhone 14.'),
(86, 133, 'pc', 'PC portable Lenovo ThinkPad X1 Carbon, i7-12e gen, 16GB RAM, 512GB SSD.', 'https://www.facebook.com/amira.saidi', 'https://wa.me/27123456', '2025-05-10 02:20:00', 'Échanger contre MacBook Air M2 ou vendre.'),
(87, 134, 'mobile', 'Huawei P50 Pro, 256GB, 8GB RAM, caméra 50MP, noir.', 'https://www.facebook.com/karim.trabelsi', 'https://wa.me/29876543', '2025-05-10 02:25:00', 'Recherche Google Pixel 8 ou Samsung Galaxy S23.'),
(88, 135, 'pc', 'PC gaming MSI, Intel i9-12900K, RTX 3090, 64GB RAM, 2TB SSD.', 'https://www.facebook.com/fatma.benali', 'https://wa.me/22334455', '2025-05-10 02:30:00', 'Vendre ou échanger contre station de travail.'),
(89, 136, 'mobile', 'Sony Xperia 1 IV, 256GB, 12GB RAM, violet, parfait état.', 'https://www.facebook.com/omar.gharbi', 'https://wa.me/26789012', '2025-05-10 02:35:00', 'Échanger contre iPhone 14 Pro ou vendre.'),
(90, 137, 'pc', 'iMac 24\" 2021, M1, 16GB RAM, 512GB SSD, bleu.', 'https://www.facebook.com/sana.mhamdi', 'https://wa.me/25432109', '2025-05-10 02:40:00', 'Recherche Mac Studio ou MacBook Pro 16\".'),
(91, 133, 'mobile', 'Oppo Find X5 Pro, 256GB, 12GB RAM, blanc céramique.', 'https://www.facebook.com/amira.saidi', 'https://wa.me/27123456', '2025-05-10 02:45:00', 'Vendre ou échanger contre Samsung Galaxy Z Fold 5.'),
(92, 134, 'pc', 'PC portable Acer Predator, i7-11e gen, RTX 3060, 16GB RAM, 1TB SSD.', 'https://www.facebook.com/karim.trabelsi', 'https://wa.me/29876543', '2025-05-10 02:50:00', 'Échanger contre PC gaming avec RTX 3080.'),
(93, 135, 'mobile', 'Samsung Galaxy Z Flip 5, 256GB, 8GB RAM, lavande.', 'https://www.facebook.com/fatma.benali', 'https://wa.me/22334455', '2025-05-10 02:55:00', 'Recherche iPhone 15 Pro ou vendre.'),
(94, 136, 'pc', 'PC de bureau custom, Ryzen 5 5600X, RTX 3060 Ti, 16GB RAM, 1TB SSD.', 'https://www.facebook.com/omar.gharbi', 'https://wa.me/26789012', '2025-05-10 03:00:00', 'Vendre ou échanger contre laptop gaming.'),
(95, 137, 'mobile', 'iPhone 14 Pro, 256GB, violet profond, batterie 95%.', 'https://www.facebook.com/sana.mhamdi', 'https://wa.me/25432109', '2025-05-10 03:05:00', 'Échanger contre Samsung Galaxy S24 Ultra.'),
(109, 138, 'pc', 'aaaa', '', 'https://wa.me/43567890', '2025-05-12 18:15:40', 'bbbbb');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nomPre` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `specialite` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `chemin_photo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nomPre`, `email`, `mot_de_passe`, `telephone`, `localisation`, `specialite`, `facebook`, `whatsapp`, `instagram`, `date_inscription`, `chemin_photo`) VALUES
(133, 'Amira Saidi', 'amira.saidi@gmail.com', '$2y$10$dpqBSC/oT.WESszI2t9YIO4VPTiuBxAm2/I5EMh2OMcwgg.XOk5Aq', '27123456', 'Tunis', 'Informatique', 'https://www.facebook.com/amira.saidi', 'https://wa.me/27123456', 'https://www.instagram.com/amira.saidi', '2025-05-10 01:00:00', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'),
(134, 'Karim Trabelsi', 'karim.trabelsi@gmail.com', '$2y$10$dpqBSC/oT.WESszI2t9YIO4VPTiuBxAm2/I5EMh2OMcwgg.XOk5Aq', '29876543', 'Sfax', 'Technicien', 'https://www.facebook.com/karim.trabelsi', 'https://wa.me/29876543', 'https://www.instagram.com/karim.trabelsi', '2025-05-10 01:05:00', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'),
(135, 'Fatma Ben Ali', 'fatma.benali@gmail.com', '$2y$10$dpqBSC/oT.WESszI2t9YIO4VPTiuBxAm2/I5EMh2OMcwgg.XOk5Aq', '22334455', 'Sousse', 'Développeur', 'https://www.facebook.com/fatma.benali', 'https://wa.me/22334455', 'https://www.instagram.com/fatma.benali', '2025-05-10 01:10:00', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'),
(136, 'Omar Gharbi', 'omar.gharbi@gmail.com', '$2y$10$dpqBSC/oT.WESszI2t9YIO4VPTiuBxAm2/I5EMh2OMcwgg.XOk5Aq', '26789012', 'Nabeul', 'Designer', 'https://www.facebook.com/omar.gharbi', 'https://wa.me/26789012', 'https://www.instagram.com/omar.gharbi', '2025-05-10 01:15:00', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'),
(137, 'Sana Mhamdi', 'sana.mhamdi@gmail.com', '$2y$10$dpqBSC/oT.WESszI2t9YIO4VPTiuBxAm2/I5EMh2OMcwgg.XOk5Aq', '25432109', 'Monastir', 'Marketing', 'https://www.facebook.com/sana.mhamdi', 'https://wa.me/25432109', 'https://www.instagram.com/sana.mhamdi', '2025-05-10 01:20:00', 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'),
(138, 'Jihed B', 'jihedb93@gmail.com', '$2y$10$3tlzUOBrIF4x/VwTG5x9fur9irweAizA58kxGeqoun.whTxLyGoEq', '+216 29810265', 'Mahdia', 'DSI2.2', 'https://facebook.com/jihedbouzidi', 'https://whatsapp.com/', 'https://www.instagram.com/', '2025-05-10 01:35:48', 'public/img.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `imagesPub`
--
ALTER TABLE `imagesPub`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_id` (`publication_id`);

--
-- Indexes for table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `publication_id` (`publication_id`);

--
-- Indexes for table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `imagesPub`
--
ALTER TABLE `imagesPub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `publication`
--
ALTER TABLE `publication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `imagesPub`
--
ALTER TABLE `imagesPub`
  ADD CONSTRAINT `imagespub_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`);

--
-- Constraints for table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`);

--
-- Constraints for table `publication`
--
ALTER TABLE `publication`
  ADD CONSTRAINT `publication_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
