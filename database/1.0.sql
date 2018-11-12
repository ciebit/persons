--
-- Estrutura para tabela `cb_persons`
--

CREATE TABLE `cb_persons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `fantasy_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `birthdate` datetime DEFAULT NULL,
  `foundation_date` datetime DEFAULT NULL,
  `type` enum('natural','legal') CHARACTER SET utf8 NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

ALTER TABLE `cb_persons`
  ADD PRIMARY KEY (`id`);
