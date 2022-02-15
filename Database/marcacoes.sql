-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 15-Fev-2022 às 00:48
-- Versão do servidor: 10.4.19-MariaDB
-- versão do PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `marcacoes`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `estudantes`
--

CREATE TABLE `estudantes` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `data_de_nascimento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `estudantes`
--

INSERT INTO `estudantes` (`id`, `nome`, `email`, `data_de_nascimento`) VALUES
(1, 'Lucílio Gomes', 'luciliodetales@gmail.com', '1999-06-01'),
(2, 'Eliúde Carvalho', 'eliude@gmail.com', '2000-02-21'),
(3, 'Pedro Domingos', 'pedro@gmail.com', '1998-06-01'),
(4, 'Ludmilo Cambambi', 'ludmilo@gmail.com', '1998-10-01'),
(5, 'Edson Chauvunge', 'edson@gmail.com', '1999-03-29'),
(6, 'Yuri Rego', 'yuri@gmail.com', '1999-12-31'),
(7, 'Rogerio Tuzolana', 'rogerio@gmail.com', '2000-04-24'),
(8, 'Isabel José', 'isabels@gmail.com', '2001-02-10'),
(9, 'Lussevádio Manuel', 'lussevadio@gmail.com', '1997-09-10'),
(10, 'Julio Manuel', 'julio@gmail.com', '2000-05-01'),
(11, 'Jacinto Malungo', 'jacinto@gmail.com', '1996-07-07'),
(12, 'Fátima Daniel', 'fatima@gmail.com', '1999-03-01'),
(13, 'Elizabeth Cristina', 'elizabeth@gmail.com', '2000-08-01'),
(14, 'Lando Garcia', 'lando@gmail.com', '0000-00-00'),
(15, 'Victor Daniel', 'victor@gmail.com', '1999-01-08'),
(16, 'Joana Cassinda', 'joana@gmail.com', '1999-05-12'),
(17, 'Joana Cassinda', 'joana@gmail.com', '0000-00-00'),
(18, 'Lucilio Gomes', 'lucilio@gmail.com', '2021-12-01'),
(19, 'Lucilio Gomes', 'lucilio@gmail.com', '2021-12-02'),
(20, 'Mario', 'lucilio@gmail.com', '2021-12-02'),
(21, 'Emanuel Carvalho', 'emanuel@gmail.com', '2021-08-03'),
(22, 'Godofredo Costa', 'godo@gmail.com', '2007-02-07'),
(23, 'Maria', 'maria@gmail.com', '2002-02-06'),
(25, 'Pedrito Costa', 'pedrito@gmail.com', '1999-02-02'),
(26, 'Marilu Silva', 'marilu@gmail.com', '2000-03-02'),
(27, 'Victor', 'luciliodetales@gmail.com', '2022-01-11'),
(28, 'Emilio Coxe', 'emi@gmail.com', '1999-02-02'),
(29, 'Emilio Cruz', 'emilio@gmail.com', '2000-02-13'),
(30, 'Emilio Cruz', 'emilio@gmail.com', '1999-12-12'),
(31, 'Kaki', 'k@gmail.com', '1999-11-12'),
(32, 'Lucílio Gomes', 'detales@gmail.com', '2000-11-11'),
(33, 'Jacinto Malungo', 'malungo@gmail.com', '1995-12-12'),
(34, 'Bibiana Filipe', 'bibi@gmail.com', '2000-12-12'),
(35, 'Lucílio Gomes', 'luciliodetales@gmail.com', '2007-02-07'),
(36, 'Verissimo', 'veri@gmail.com', '1999-12-12');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estudante_na_turma`
--

CREATE TABLE `estudante_na_turma` (
  `id` int(11) NOT NULL,
  `id_estudante` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `estudante_na_turma`
--

INSERT INTO `estudante_na_turma` (`id`, `id_estudante`, `id_turma`) VALUES
(11, 10, 2),
(12, 11, 2),
(13, 12, 2),
(23, 2, 11),
(24, 3, 11),
(25, 5, 11),
(29, 22, 11),
(32, 25, 2),
(33, 26, 11),
(34, 27, 11),
(36, 29, 11),
(39, 32, 11),
(41, 34, 2),
(42, 35, 15),
(43, 34, 11),
(44, 31, 19),
(45, 15, 19),
(46, 7, 19),
(47, 5, 19),
(48, 9, 19),
(49, 17, 19),
(50, 33, 19),
(51, 36, 19),
(52, 17, 11);

-- --------------------------------------------------------

--
-- Estrutura da tabela `marcacao_estudante`
--

CREATE TABLE `marcacao_estudante` (
  `id` int(11) NOT NULL,
  `id_marcacao` int(11) NOT NULL,
  `id_estudante` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `marcacao_estudante`
--

INSERT INTO `marcacao_estudante` (`id`, `id_marcacao`, `id_estudante`) VALUES
(1, 1, 8),
(2, 2, 10),
(3, 3, 11),
(4, 4, 12),
(5, 5, 13),
(6, 6, 16),
(7, 7, 3),
(9, 9, 22),
(10, 10, 12),
(11, 11, 3),
(12, 12, 2),
(13, 13, 16),
(16, 16, 8),
(17, 17, 12),
(18, 18, 11),
(19, 19, 10),
(20, 20, 23),
(21, 22, 8),
(35, 36, 2),
(36, 37, 26),
(37, 38, 34),
(38, 39, 32),
(39, 40, 35),
(40, 41, 36),
(41, 42, 31),
(42, 43, 15),
(43, 44, 7),
(44, 45, 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `marcacoes`
--

CREATE TABLE `marcacoes` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL DEFAULT current_timestamp(),
  `id_turma` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `marcacoes`
--

INSERT INTO `marcacoes` (`id`, `data`, `id_turma`, `estado`) VALUES
(1, '2021-12-27', 2, 1),
(2, '2021-12-27', 2, 0),
(3, '2021-12-27', 2, 1),
(4, '2021-12-27', 2, 0),
(5, '2021-12-27', 2, 1),
(6, '2021-12-27', 11, 1),
(7, '2021-12-27', 11, 0),
(9, '2021-12-27', 11, 1),
(10, '2021-12-26', 2, 0),
(11, '2021-12-28', 11, 0),
(12, '2021-12-28', 11, 1),
(13, '2021-12-28', 11, 0),
(16, '2021-12-28', 2, 0),
(17, '2021-12-28', 2, 0),
(18, '2021-12-28', 2, 0),
(19, '2021-12-28', 2, 1),
(20, '2021-12-29', 11, 0),
(21, '2022-02-09', 11, 1),
(22, '2022-02-09', 11, 1),
(36, '2022-02-12', 11, 1),
(37, '2022-02-12', 11, 0),
(38, '2022-02-12', 11, 1),
(39, '2022-02-12', 11, 1),
(40, '2022-02-12', 15, 1),
(41, '2022-02-12', 19, 1),
(42, '2022-02-12', 19, 0),
(43, '2022-02-12', 19, 1),
(44, '2022-02-12', 19, 0),
(45, '2022-02-12', 19, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `turmas`
--

INSERT INTO `turmas` (`id`, `descricao`, `id_user`) VALUES
(2, 'CC_POO', 2),
(11, 'CC-AC', 1),
(15, 'ccdd', 3),
(18, 'CC-MIC', 3),
(19, 'CC-MIC', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `acess` varchar(10) NOT NULL DEFAULT 'docente',
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `name`, `email`, `password`, `acess`, `status`) VALUES
(1, 'Lucílio Gomes', 'lucilio@gmail.com', '$2y$10$FIy4uIQaXg19NEYXVC3JOutn/DHXVZdBYoRYQVwuPBRA.iYMQJjTm', 'docente', 1),
(2, 'Lufialuizo Sampaio', 'sampaio@gmail.com', '$2y$10$Jkn7aFL2gYfBlXfype0CaeZVggfnWtCQJyo5ms.CP96DgAKfxOcVe', 'docente', 1),
(3, 'Amândio Almada', 'amandio@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'docente', 1),
(4, 'Vicente Lopes', 'vicente@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'docente', 0),
(5, 'Administrador', 'admin@gmail.com', '$2y$10$7hiwm4QPo4YP6A362bdpheG0fY1Bv7OuXrggITnfK3aU2eHvmGGbi', 'admin', 1),
(7, 'Victor', 'victor@gmail.com', '$2y$10$RT9RQu25/pry3OvvjRtvL.8ACWjunLZUMHIPzY.weWdCJHGhAnv1i', 'admin', 1),
(8, 'DD', 'ddetales@gmail.com', '$2y$10$Sg2TKsC4ksWP9NEDJdEvberem1gGKiV27keT.S0MIYy869mfVIUzK', 'docente', 1),
(9, 'Lucílio Gomes', 'ludciliodetales@gmail.com', '$2y$10$crqh0z17dMN.Fef7zVoqXOhxd.FDGFssBjUfQ7Hm4qjnxyir/rRIu', 'docente', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `estudantes`
--
ALTER TABLE `estudantes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `estudante_na_turma`
--
ALTER TABLE `estudante_na_turma`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estudante` (`id_estudante`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Índices para tabela `marcacao_estudante`
--
ALTER TABLE `marcacao_estudante`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_marcacao` (`id_marcacao`);

--
-- Índices para tabela `marcacoes`
--
ALTER TABLE `marcacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Índices para tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `estudantes`
--
ALTER TABLE `estudantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `estudante_na_turma`
--
ALTER TABLE `estudante_na_turma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `marcacao_estudante`
--
ALTER TABLE `marcacao_estudante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `marcacoes`
--
ALTER TABLE `marcacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `estudante_na_turma`
--
ALTER TABLE `estudante_na_turma`
  ADD CONSTRAINT `estudante_na_turma_ibfk_1` FOREIGN KEY (`id_estudante`) REFERENCES `estudantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `estudante_na_turma_ibfk_2` FOREIGN KEY (`id_turma`) REFERENCES `turmas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `marcacao_estudante`
--
ALTER TABLE `marcacao_estudante`
  ADD CONSTRAINT `marcacao_estudante_ibfk_1` FOREIGN KEY (`id_marcacao`) REFERENCES `marcacoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `marcacoes`
--
ALTER TABLE `marcacoes`
  ADD CONSTRAINT `marcacoes_ibfk_1` FOREIGN KEY (`id_turma`) REFERENCES `turmas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `turmas`
--
ALTER TABLE `turmas`
  ADD CONSTRAINT `turmas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
