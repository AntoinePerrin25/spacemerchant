-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 192.168.1.19
-- Généré le : jeu. 16 nov. 2023 à 13:00
-- Version du serveur : 11.1.2-MariaDB-1:11.1.2+maria~ubu2204
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `spacemerchant`
--

-- --------------------------------------------------------
-
--
-- Structure de la table `Cargo`
--

CREATE TABLE `Cargo` (
  `CargoID` int(11) NOT NULL,
  `CargoName` varchar(40) DEFAULT NULL,
  `CargoSize` int(11) DEFAULT NULL,
  `TransportedByShip` int(11) DEFAULT NULL,
  `TransportedByCrew` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Cargo`
--

INSERT INTO `Cargo` (`CargoID`, `CargoName`, `CargoSize`, `TransportedByShip`, `TransportedByCrew`) VALUES
(1, 'Electronics', 200, NULL, NULL),
(2, 'Gold', 500, NULL, NULL),
(3, 'Minerals', 400, NULL, NULL),
(4, 'Lapis Lazuli', 300, NULL, NULL),
(5, 'Ruby', 50, NULL, NULL),
(6, 'Emerald', 50, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `Crew`
--

CREATE TABLE `Crew` (
  `CrewID` int(11) NOT NULL,
  `CrewName` varchar(40) DEFAULT NULL,
  `CrewOwnerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Crew`
--

INSERT INTO `Crew` (`CrewID`, `CrewName`, `CrewOwnerID`) VALUES
(1, 'UTBM', 1),
(2, 'Autres', 2),
(3, 'La bas eux 1A', 1),
(4, 'Eux la bas A1', 1);

-- --------------------------------------------------------

--
-- Structure de la table `CrewMember`
--

CREATE TABLE `CrewMember` (
  `MemberID` int(11) NOT NULL,
  `Name` varchar(20) DEFAULT NULL,
  `CrewID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CrewMember`
--

INSERT INTO `CrewMember` (`MemberID`, `Name`, `CrewID`) VALUES
(1, 'Bob', 1),
(2, 'Anna', 1),
(3, 'Jean', 2),
(4, 'Patrick', 2);

-- --------------------------------------------------------

--
-- Structure de la table `missions`
--

CREATE TABLE `missions` (
  `MissionID` int(11) NOT NULL,
  `MissionName` varchar(40) DEFAULT NULL,
  `Reward` int(11) DEFAULT NULL,
  `Succeeded` tinyint(1) DEFAULT NULL,
  `PostedBy` int(11) DEFAULT NULL,
  `AcceptedBy` int(11) DEFAULT NULL,
  `FromPlanetID` int(11) DEFAULT NULL,
  `ToPlanetID` int(11) DEFAULT NULL,
  `CargoID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `missions`
--

INSERT INTO `missions` (`MissionID`, `MissionName`, `Reward`, `Succeeded`, `PostedBy`, `AcceptedBy`, `FromPlanetID`, `ToPlanetID`, `CargoID`) VALUES
(5, 'Delivery to Alpha Centauri', 500, 0, 2, NULL, 1, 2, 1),
(6, 'Exploration of Proxima b', 800, 0, 2, NULL, 2, 3, 2),
(13, 'Transportation of Gold', 600, NULL, 2, NULL, 2, 3, 3),
(14, 'Lapis Lazuli Transportation', 200, NULL, 2, NULL, 1, 3, 4);

-- --------------------------------------------------------

--
-- Structure de la table `Planet`
--

CREATE TABLE `Planet` (
  `PlanetID` int(11) NOT NULL,
  `PlanetName` varchar(40) DEFAULT NULL,
  `FuelPrice` int(11) DEFAULT NULL,
  `CoordX` int(11) DEFAULT NULL,
  `CoordY` int(11) DEFAULT NULL,
  `CoordZ` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Planet`
--

INSERT INTO `Planet` (`PlanetID`, `PlanetName`, `FuelPrice`, `CoordX`, `CoordY`, `CoordZ`) VALUES
(1, 'Earth', 5, 0, 0, 0),
(2, 'Alpha Centauri', 8, 10, 5, 3),
(3, 'Proxima b', 7, 8, 3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `Spaceship`
--

CREATE TABLE `Spaceship` (
  `SpaceshipID` int(11) NOT NULL,
  `Fuel` int(11) DEFAULT NULL,
  `ModelID` int(11) DEFAULT NULL,
  `OwnerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Spaceship`
--

INSERT INTO `Spaceship` (`SpaceshipID`, `Fuel`, `ModelID`, `OwnerID`) VALUES
(1, 100, 1, 1),
(2, 100, 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `SpaceshipModel`
--

CREATE TABLE `SpaceshipModel` (
  `ModelID` int(11) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `Size` int(11) DEFAULT NULL,
  `Price` int(11) DEFAULT NULL,
  `FuelEfficiency` int(11) DEFAULT NULL,
  `FuelCapacity` int(11) DEFAULT NULL,
  `Speed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `SpaceshipModel`
--

INSERT INTO `SpaceshipModel` (`ModelID`, `Name`, `Size`, `Price`, `FuelEfficiency`, `FuelCapacity`, `Speed`) VALUES
(1, 'Space Cruiser', 600, 1000, 10, 500, 1000),
(2, 'X-Wing', 100, 1500, 15, 500, 2000);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `Password` varchar(72) NOT NULL,
  `creation_date` date DEFAULT current_timestamp(),
  `Account` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`UserID`, `Username`, `Password`, `creation_date`, `Account`) VALUES
(1, 'AntoineP', '$2y$12$25QFmxIfC.8MFXL0qwvPxODiw3FkqzPVaxwcGi.5Lh8hBFWsy3WN.', '2023-11-06', NULL),
(2, 'NaNo', '$2y$12$GXngcILKRNMHZCJiE7.vM.S0VwFzk6mSKWBgsbnNE2r3TODK72U3W', '2023-11-11', NULL),
(3, 'admin', '$2y$12$TNgnQHHO3nzqlLUpM4/cFOYLSrtoAMYHXTNlaf1MhRm52YljVp.gq', '2023-11-16', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Cargo`
--
ALTER TABLE `Cargo`
  ADD PRIMARY KEY (`CargoID`),
  ADD KEY `TransportedByShip` (`TransportedByShip`),
  ADD KEY `TransportedByCrew` (`TransportedByCrew`);

--
-- Index pour la table `Crew`
--
ALTER TABLE `Crew`
  ADD PRIMARY KEY (`CrewID`),
  ADD KEY `CrewOwnerID` (`CrewOwnerID`);

--
-- Index pour la table `CrewMember`
--
ALTER TABLE `CrewMember`
  ADD PRIMARY KEY (`MemberID`),
  ADD KEY `CrewID` (`CrewID`);

--
-- Index pour la table `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`MissionID`),
  ADD KEY `PostedBy` (`PostedBy`),
  ADD KEY `AcceptedBy` (`AcceptedBy`),
  ADD KEY `FromPlanetID` (`FromPlanetID`),
  ADD KEY `ToPlanetID` (`ToPlanetID`),
  ADD KEY `CargoID` (`CargoID`);

--
-- Index pour la table `Planet`
--
ALTER TABLE `Planet`
  ADD PRIMARY KEY (`PlanetID`);

--
-- Index pour la table `Spaceship`
--
ALTER TABLE `Spaceship`
  ADD PRIMARY KEY (`SpaceshipID`),
  ADD KEY `ModelID` (`ModelID`),
  ADD KEY `OwnerID` (`OwnerID`);

--
-- Index pour la table `SpaceshipModel`
--
ALTER TABLE `SpaceshipModel`
  ADD PRIMARY KEY (`ModelID`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Cargo`
--
ALTER TABLE `Cargo`
  MODIFY `CargoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `Crew`
--
ALTER TABLE `Crew`
  MODIFY `CrewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `CrewMember`
--
ALTER TABLE `CrewMember`
  MODIFY `MemberID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `missions`
--
ALTER TABLE `missions`
  MODIFY `MissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `Planet`
--
ALTER TABLE `Planet`
  MODIFY `PlanetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `Spaceship`
--
ALTER TABLE `Spaceship`
  MODIFY `SpaceshipID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `SpaceshipModel`
--
ALTER TABLE `SpaceshipModel`
  MODIFY `ModelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Cargo`
--
ALTER TABLE `Cargo`
  ADD CONSTRAINT `Cargo_ibfk_1` FOREIGN KEY (`TransportedByShip`) REFERENCES `Spaceship` (`SpaceshipID`),
  ADD CONSTRAINT `Cargo_ibfk_2` FOREIGN KEY (`TransportedByCrew`) REFERENCES `Crew` (`CrewID`);

--
-- Contraintes pour la table `Crew`
--
ALTER TABLE `Crew`
  ADD CONSTRAINT `Crew_ibfk_1` FOREIGN KEY (`CrewOwnerID`) REFERENCES `user` (`UserID`);

--
-- Contraintes pour la table `CrewMember`
--
ALTER TABLE `CrewMember`
  ADD CONSTRAINT `CrewMember_ibfk_1` FOREIGN KEY (`CrewID`) REFERENCES `Crew` (`CrewID`);

--
-- Contraintes pour la table `missions`
--
ALTER TABLE `missions`
  ADD CONSTRAINT `missions_ibfk_1` FOREIGN KEY (`PostedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `missions_ibfk_2` FOREIGN KEY (`AcceptedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `missions_ibfk_3` FOREIGN KEY (`FromPlanetID`) REFERENCES `Planet` (`PlanetID`),
  ADD CONSTRAINT `missions_ibfk_4` FOREIGN KEY (`ToPlanetID`) REFERENCES `Planet` (`PlanetID`),
  ADD CONSTRAINT `missions_ibfk_5` FOREIGN KEY (`CargoID`) REFERENCES `Cargo` (`CargoID`);

--
-- Contraintes pour la table `Spaceship`
--
ALTER TABLE `Spaceship`
  ADD CONSTRAINT `Spaceship_ibfk_1` FOREIGN KEY (`ModelID`) REFERENCES `SpaceshipModel` (`ModelID`),
  ADD CONSTRAINT `Spaceship_ibfk_2` FOREIGN KEY (`OwnerID`) REFERENCES `user` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
