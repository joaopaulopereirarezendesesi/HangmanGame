-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/03/2025 às 01:17
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
  `ID_T` bigint(20) UNSIGNED NOT NULL,
  `ID_ROUND` bigint(20) UNSIGNED NOT NULL,
  `GUESS` varchar(255) NOT NULL,
  `IS_CORRECT` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `friends`
--

CREATE TABLE `friends` (
  `ID_U` bigint(20) UNSIGNED NOT NULL,
  `ID_A` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `friend_requests`
--

CREATE TABLE `friend_requests` (
  `id` bigint(11) NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `photos`
--

CREATE TABLE `photos` (
  `MATTER` varchar(255) NOT NULL,
  `ADDRESS` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `played`
--

CREATE TABLE `played` (
  `ID_PLAYED` bigint(20) UNSIGNED NOT NULL,
  `ID_U` bigint(20) UNSIGNED NOT NULL,
  `ID_R` bigint(20) UNSIGNED NOT NULL,
  `SCORE` int(11) DEFAULT 0,
  `IS_THE_CHALLENGER` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--
-- Acionadores `played`
--
DELIMITER $$
CREATE TRIGGER `EmptyRoomTrigger` AFTER DELETE ON `played` FOR EACH ROW BEGIN
    DECLARE player_count INT;

    SELECT COUNT(*) INTO player_count
    FROM played
    WHERE ID_R = OLD.ID_R;

    IF player_count = 0 THEN
        DELETE FROM rooms WHERE ID_R = OLD.ID_R;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ranking`
--

CREATE TABLE `ranking` (
  `ID_U` bigint(20) UNSIGNED NOT NULL,
  `POSITION` int(11) DEFAULT NULL,
  `AMOUNT_OF_WINS` int(11) DEFAULT 0,
  `NUMBER_OF_GAMES` int(11) DEFAULT 0,
  `POINT_AMOUNT` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rooms`
--

CREATE TABLE `rooms` (
  `ID_R` bigint(20) UNSIGNED NOT NULL,
  `ROOM_NAME` varchar(100) NOT NULL,
  `ID_O` bigint(20) UNSIGNED NOT NULL,
  `PRIVATE` tinyint(1) DEFAULT 0,
  `PASSWORD` varchar(50) DEFAULT NULL,
  `PLAYER_CAPACITY` int(11) DEFAULT 10,
  `TIME_LIMIT` int(11) DEFAULT 5,
  `POINTS` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rounds`
--

CREATE TABLE `rounds` (
  `ID_ROUND` bigint(20) UNSIGNED NOT NULL,
  `ID_R` bigint(20) UNSIGNED NOT NULL,
  `PLAYER_OF_THE_TIME` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `ID_U` bigint(20) UNSIGNED NOT NULL,
  `NICKNAME` varchar(50) NOT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `PASSWORD` varchar(255) DEFAULT NULL,
  `ONLINE` tinyint(1) NOT NULL,
  `PHOTO` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `wordsmatter`
--

CREATE TABLE `wordsmatter` (
  `id` int(11) NOT NULL,
  `matter` varchar(255) NOT NULL,
  `word` varchar(255) NOT NULL,
  `definition` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_friend_requests_sender` (`sender_id`),
  ADD KEY `fk_friend_requests_receiver` (`receiver_id`);

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
  ADD PRIMARY KEY (`ID_ROUND`),
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
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `attempts`
--
ALTER TABLE `attempts`
  MODIFY `ID_T` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `played`
--
ALTER TABLE `played`
  MODIFY `ID_PLAYED` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `rooms`
--
ALTER TABLE `rooms`
  MODIFY `ID_R` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `rounds`
--
ALTER TABLE `rounds`
  MODIFY `ID_ROUND` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `ID_U` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de tabela `wordsmatter`
--
ALTER TABLE `wordsmatter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `fk_attempts_round` FOREIGN KEY (`ID_ROUND`) REFERENCES `rounds` (`ID_ROUND`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_friend_requests_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_friend_requests_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`ID_U`) ON DELETE CASCADE;

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
