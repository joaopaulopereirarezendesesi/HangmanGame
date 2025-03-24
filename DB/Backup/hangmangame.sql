-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/03/2025 às 02:59
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `hangmangame`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `attempts`
--

CREATE TABLE `attempts` (
  `ID_T` char(36) NOT NULL,
  `ID_ROUND` char(36) NOT NULL,
  `GUESS` varchar(255) NOT NULL,
  `IS_CORRECT` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `friends`
--

CREATE TABLE `friends` (
  `ID_U` char(36) NOT NULL,
  `ID_A` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `friend_requests`
--

CREATE TABLE `friend_requests` (
  `ID` char(36) NOT NULL,
  `SENDER_ID` char(36) NOT NULL,
  `RECEIVER_ID` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `photos`
--

CREATE TABLE `photos` (
  `ID_PH` char(36) NOT NULL,
  `MATTER` varchar(255) NOT NULL,
  `ADDRESS` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `photos`
--

INSERT INTO `photos` (`ID_PH`, `MATTER`, `ADDRESS`) VALUES
('5c72027d-0851-11f0-94e0-74563cdb9d1a', 'livre', 'http://localhost:80/assets/photos/livre.png'),
('7a3a84da-0846-11f0-94e0-74563cdb9d1a', 'antropologia', 'http://localhost:80/assets/photos/antropologia.png'),
('7a3a9632-0846-11f0-94e0-74563cdb9d1a', 'biologia', 'http://localhost:80/assets/photos/biologia.png'),
('7a3a9697-0846-11f0-94e0-74563cdb9d1a', 'cienciapolitica', 'http://localhost:80/assets/photos/cienciapolitica.png'),
('7a3a96c7-0846-11f0-94e0-74563cdb9d1a', 'filosofia', 'http://localhost:80/assets/photos/filosofia.png'),
('7a3a96ea-0846-11f0-94e0-74563cdb9d1a', 'fisica', 'http://localhost:80/assets/photos/fisica.png'),
('7a3a970f-0846-11f0-94e0-74563cdb9d1a', 'geografia', 'http://localhost:80/assets/photos/geografia.png'),
('7a3a9745-0846-11f0-94e0-74563cdb9d1a', 'historia', 'http://localhost:80/assets/photos/historia.png'),
('7a3a9777-0846-11f0-94e0-74563cdb9d1a', 'matematica', 'http://localhost:80/assets/photos/matematica.png'),
('7a3a979c-0846-11f0-94e0-74563cdb9d1a', 'psicologia', 'http://localhost:80/assets/photos/psicologia.png'),
('7a3a97c0-0846-11f0-94e0-74563cdb9d1a', 'sociologia', 'http://localhost:80/assets/photos/sociologia.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `played`
--

CREATE TABLE `played` (
  `ID_PLAYED` char(36) NOT NULL,
  `ID_U` char(36) NOT NULL,
  `ID_R` char(36) NOT NULL,
  `SCORE` int(11) DEFAULT 0,
  `IS_THE_CHALLENGER` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ranking`
--

CREATE TABLE `ranking` (
  `ID_U` char(36) NOT NULL,
  `POSITION` int(11) NOT NULL,
  `AMOUNT_OF_WINS` int(11) NOT NULL DEFAULT 0,
  `NUMBER_OF_GAMES` int(11) NOT NULL DEFAULT 0,
  `POINT_AMOUNT` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rooms`
--

CREATE TABLE `rooms` (
  `ID_R` char(36) NOT NULL,
  `ROOM_NAME` varchar(100) NOT NULL,
  `ID_O` char(36) NOT NULL,
  `PRIVATE` tinyint(1) NOT NULL DEFAULT 0,
  `PASSWORD` varchar(50) DEFAULT NULL,
  `PLAYER_CAPACITY` int(11) NOT NULL DEFAULT 10,
  `TIME_LIMIT` int(11) NOT NULL DEFAULT 5,
  `POINTS` int(11) NOT NULL,
  `MODALITY` varchar(255) NOT NULL,
  `MODALITY_IMG` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rounds`
--

CREATE TABLE `rounds` (
  `ID_RD` char(36) NOT NULL,
  `ID_R` char(36) NOT NULL,
  `PLAYER_OF_THE_TIME` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `ID_U` char(36) NOT NULL,
  `NICKNAME` varchar(50) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `ONLINE` enum('offline','online','away') NOT NULL DEFAULT 'offline',
  `PHOTO` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `wordsmatter`
--

CREATE TABLE `wordsmatter` (
  `ID_W` char(36) NOT NULL,
  `MATTER` varchar(255) NOT NULL,
  `WORD` varchar(255) NOT NULL,
  `DEFINITION` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `attempts`
--
ALTER TABLE `attempts`
  ADD PRIMARY KEY (`ID_T`),
  ADD KEY `ID_ROUND` (`ID_ROUND`);

--
-- Índices de tabela `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`ID_U`,`ID_A`),
  ADD KEY `fk_friends_a` (`ID_A`);

--
-- Índices de tabela `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_friend_requests_sender` (`SENDER_ID`),
  ADD KEY `fk_friend_requests_receiver` (`RECEIVER_ID`);

--
-- Índices de tabela `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`ID_PH`);

--
-- Índices de tabela `played`
--
ALTER TABLE `played`
  ADD PRIMARY KEY (`ID_PLAYED`),
  ADD KEY `ID_U` (`ID_U`),
  ADD KEY `ID_R` (`ID_R`);

--
-- Índices de tabela `ranking`
--
ALTER TABLE `ranking`
  ADD PRIMARY KEY (`ID_U`);

--
-- Índices de tabela `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`ID_R`),
  ADD KEY `ID_O` (`ID_O`);

--
-- Índices de tabela `rounds`
--
ALTER TABLE `rounds`
  ADD PRIMARY KEY (`ID_RD`),
  ADD KEY `ID_R` (`ID_R`),
  ADD KEY `PLAYER_OF_THE_TIME` (`PLAYER_OF_THE_TIME`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID_U`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

--
-- Índices de tabela `wordsmatter`
--
ALTER TABLE `wordsmatter`
  ADD PRIMARY KEY (`ID_W`);

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `fk_attempts_round` FOREIGN KEY (`ID_ROUND`) REFERENCES `rounds` (`ID_RD`) ON DELETE CASCADE;

--
-- Restrições para tabelas `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `fk_friends_a` FOREIGN KEY (`ID_A`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_friends_u` FOREIGN KEY (`ID_U`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE;

--
-- Restrições para tabelas `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD CONSTRAINT `fk_friend_requests_receiver` FOREIGN KEY (`RECEIVER_ID`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_friend_requests_sender` FOREIGN KEY (`SENDER_ID`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE;

--
-- Restrições para tabelas `played`
--
ALTER TABLE `played`
  ADD CONSTRAINT `fk_played_room` FOREIGN KEY (`ID_R`) REFERENCES `rooms` (`ID_R`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_played_user` FOREIGN KEY (`ID_U`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE;

--
-- Restrições para tabelas `ranking`
--
ALTER TABLE `ranking`
  ADD CONSTRAINT `fk_ranking_u` FOREIGN KEY (`ID_U`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE;

--
-- Restrições para tabelas `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_owner` FOREIGN KEY (`ID_O`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE;

--
-- Restrições para tabelas `rounds`
--
ALTER TABLE `rounds`
  ADD CONSTRAINT `fk_rounds_player` FOREIGN KEY (`PLAYER_OF_THE_TIME`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rounds_room` FOREIGN KEY (`ID_R`) REFERENCES `rooms` (`ID_R`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
