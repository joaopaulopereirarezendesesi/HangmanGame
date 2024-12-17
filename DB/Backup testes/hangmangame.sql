-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/12/2024 às 19:28
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
  `ID_U` int(11) DEFAULT NULL,
  `ID_A` int(11) DEFAULT NULL
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
-- Despejando dados para a tabela `played`
--

INSERT INTO `played` (`ID_PLAYED`, `ID_U`, `ID_R`, `SCORE`, `IS_THE_CHALLENGER`) VALUES
(1, 35, 8, 0, 0),
(2, 36, 8, 0, 0),
(3, 37, 8, 0, 0),
(4, 38, 8, 0, 0),
(5, 39, 8, 0, 0),
(6, 40, 8, 0, 0),
(7, 41, 8, 0, 0),
(8, 42, 8, 0, 0),
(9, 43, 8, 0, 0),
(10, 44, 8, 0, 0),
(11, 45, 9, 0, 0),
(12, 46, 9, 0, 0),
(13, 47, 9, 0, 0),
(14, 48, 9, 0, 0),
(15, 49, 9, 0, 0),
(16, 50, 9, 0, 0),
(17, 51, 9, 0, 0),
(18, 52, 9, 0, 0),
(19, 53, 9, 0, 0),
(20, 54, 9, 0, 0),
(21, 55, 10, 0, 0),
(22, 56, 10, 0, 0),
(23, 57, 10, 0, 0),
(24, 58, 10, 0, 0),
(25, 59, 10, 0, 0),
(26, 60, 10, 0, 0),
(27, 61, 10, 0, 0),
(28, 62, 10, 0, 0),
(29, 63, 10, 0, 0),
(30, 64, 10, 0, 0);

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

--
-- Despejando dados para a tabela `rooms`
--

INSERT INTO `rooms` (`ID_R`, `ROOM_NAME`, `ID_O`, `PRIVATE`, `PASSWORD`, `PLAYER_CAPACITY`, `TIME_LIMIT`, `POINTS`) VALUES
(8, 'Room 1', 65, 0, NULL, 10, 5, 100),
(9, 'Room 2', 66, 0, NULL, 10, 5, 150),
(10, 'Room 3', 67, 0, NULL, 10, 5, 200);

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
  `PASSWORD` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`ID_U`, `NICKNAME`, `EMAIL`, `PASSWORD`) VALUES
(35, 'player1', 'player1@example.com', 'pass123'),
(36, 'player2', 'player2@example.com', 'pass123'),
(37, 'player3', 'player3@example.com', 'pass123'),
(38, 'player4', 'player4@example.com', 'pass123'),
(39, 'player5', 'player5@example.com', 'pass123'),
(40, 'player6', 'player6@example.com', 'pass123'),
(41, 'player7', 'player7@example.com', 'pass123'),
(42, 'player8', 'player8@example.com', 'pass123'),
(43, 'player9', 'player9@example.com', 'pass123'),
(44, 'player10', 'player10@example.com', 'pass123'),
(45, 'player11', 'player11@example.com', 'pass123'),
(46, 'player12', 'player12@example.com', 'pass123'),
(47, 'player13', 'player13@example.com', 'pass123'),
(48, 'player14', 'player14@example.com', 'pass123'),
(49, 'player15', 'player15@example.com', 'pass123'),
(50, 'player16', 'player16@example.com', 'pass123'),
(51, 'player17', 'player17@example.com', 'pass123'),
(52, 'player18', 'player18@example.com', 'pass123'),
(53, 'player19', 'player19@example.com', 'pass123'),
(54, 'player20', 'player20@example.com', 'pass123'),
(55, 'player21', 'player21@example.com', 'pass123'),
(56, 'player22', 'player22@example.com', 'pass123'),
(57, 'player23', 'player23@example.com', 'pass123'),
(58, 'player24', 'player24@example.com', 'pass123'),
(59, 'player25', 'player25@example.com', 'pass123'),
(60, 'player26', 'player26@example.com', 'pass123'),
(61, 'player27', 'player27@example.com', 'pass123'),
(62, 'player28', 'player28@example.com', 'pass123'),
(63, 'player29', 'player29@example.com', 'pass123'),
(64, 'player30', 'player30@example.com', 'pass123'),
(65, 'owner1', 'owner1@example.com', 'pass123'),
(66, 'owner2', 'owner2@example.com', 'pass123'),
(67, 'owner3', 'owner3@example.com', 'pass123');

-- --------------------------------------------------------

--
-- Estrutura para tabela `wordsmatter`
--

CREATE TABLE `wordsmatter` (
  `matter` varchar(255) DEFAULT NULL,
  `word` varchar(255) DEFAULT NULL,
  `definition` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `wordsmatter`
--

INSERT INTO `wordsmatter` (`matter`, `word`, `definition`) VALUES
('Geografia', 'Continente', 'Grande massa de terra delimitada pelos oceanos.'),
('Geografia', 'Latitude', 'Distância angular de um ponto em relação ao Equador.'),
('Geografia', 'Longitude', 'Distância angular de um ponto em relação ao Meridiano de Greenwich.'),
('Geografia', 'Relevo', 'Formas da superfície terrestre, como montanhas e planícies.'),
('Geografia', 'Bioma', 'Grande conjunto de ecossistemas com características semelhantes.'),
('Geografia', 'Tectônica de Placas', 'Teoria que explica o movimento das placas tectônicas da Terra.'),
('Geografia', 'Erosão', 'Desgaste das rochas e do solo por agentes naturais.'),
('Geografia', 'Clima', 'Conjunto de condições atmosféricas de uma região por um longo período.'),
('Geografia', 'Vegetação', 'Conjunto de plantas que crescem naturalmente em uma região.'),
('Geografia', 'Hidrografia', 'Estudo das águas na superfície terrestre.'),
('Física', 'Força', 'Interação que muda o estado de movimento de um objeto.'),
('Física', 'Energia', 'Capacidade de realizar trabalho ou produzir movimento.'),
('Física', 'Movimento', 'Mudança de posição de um corpo ao longo do tempo.'),
('Física', 'Velocidade', 'Relação entre o deslocamento e o tempo.'),
('Física', 'Gravidade', 'Força que atrai corpos para o centro da Terra.'),
('Física', 'Massa', 'Quantidade de matéria em um corpo.'),
('Física', 'Trabalho', 'Energia transferida para realizar um deslocamento.'),
('Física', 'Fricção', 'Força que resiste ao movimento relativo entre superfícies.'),
('Física', 'Potência', 'Taxa de realização de trabalho ou consumo de energia.'),
('Física', 'Inércia', 'Tendência de um corpo de manter seu estado de movimento.'),
('Biologia', 'Fotossíntese', 'Processo pelo qual plantas convertem luz em energia química.'),
('Biologia', 'Célula', 'Unidade básica da vida dos organismos vivos.'),
('Biologia', 'Mitose', 'Processo de divisão celular que resulta em duas células idênticas.'),
('Biologia', 'Ecossistema', 'Conjunto de organismos vivos interagindo com o ambiente.'),
('Biologia', 'Gene', 'Unidade de hereditariedade que determina características dos organismos.'),
('Biologia', 'DNA', 'Molécula que contém informações genéticas dos seres vivos.'),
('Biologia', 'Habitat', 'Ambiente natural onde vive um organismo.'),
('Biologia', 'Respiração celular', 'Processo de produção de energia em células.'),
('Biologia', 'Organismo', 'Qualquer ser vivo, como animais, plantas e microrganismos.'),
('Biologia', 'Enzima', 'Molécula que acelera reações químicas nos organismos vivos.'),
('História', 'Civilização', 'Sociedade com cultura e organização complexas.'),
('História', 'Revolução', 'Mudança drástica e significativa em uma sociedade.'),
('História', 'Monarquia', 'Forma de governo liderada por um rei ou rainha.'),
('História', 'Democracia', 'Sistema político onde o poder é exercido pelo povo.'),
('História', 'Colonização', 'Processo de ocupação e exploração de um território por outro país.'),
('História', 'Escravidão', 'Sistema onde pessoas são propriedade de outras.'),
('História', 'Renascimento', 'Movimento cultural e artístico da Europa nos séculos XIV-XVI.'),
('História', 'Guerra Fria', 'Conflito ideológico entre EUA e URSS no século XX.'),
('História', 'Feudalismo', 'Sistema econômico e social baseado em terras e servidão.'),
('História', 'Revolução Industrial', 'Transição para processos de produção mecanizados.'),
('Matemática', 'Equação', 'Expressão matemática que afirma a igualdade entre duas partes.'),
('Matemática', 'Função', 'Relação entre um conjunto de entradas e saídas.'),
('Matemática', 'Geometria', 'Estudo das formas, tamanhos e propriedades do espaço.'),
('Matemática', 'Álgebra', 'Ramo da matemática que usa símbolos para resolver problemas.'),
('Matemática', 'Cálculo', 'Estudo das taxas de variação e acumulação.'),
('Matemática', 'Probabilidade', 'Medida da chance de um evento ocorrer.'),
('Matemática', 'Estatística', 'Ramo que coleta, analisa e interpreta dados.'),
('Matemática', 'Logaritmo', 'Inverso da exponenciação, usado em cálculos avançados.'),
('Matemática', 'Matriz', 'Tabela de números organizados em linhas e colunas.'),
('Matemática', 'Hipotenusa', 'Maior lado de um triângulo retângulo.'),
('Geografia', 'Clima', 'Condições atmosféricas típicas de uma região ao longo do tempo.'),
('Geografia', 'Mapa', 'Representação gráfica de uma área geográfica.'),
('Geografia', 'Cartografia', 'Ciência de criar mapas e representações da Terra.'),
('Geografia', 'Oceano', 'Grande massa de água salgada que cobre a maior parte da superfície terrestre.'),
('Geografia', 'Deserto', 'Região com pouca precipitação e vegetação escassa.'),
('Geografia', 'Floresta', 'Área densa de árvores e vegetação variada.'),
('Geografia', 'Urbanização', 'Processo de crescimento e desenvolvimento das cidades.'),
('Geografia', 'Globalização', 'Integração econômica, social e cultural entre países.'),
('Geografia', 'Savana', 'Bioma tropical caracterizado por gramíneas e árvores espaçadas.'),
('Geografia', 'Região', 'Área delimitada com características geográficas ou culturais comuns.'),
('Física', 'Eletricidade', 'Fenômeno relacionado ao fluxo de cargas elétricas.'),
('Física', 'Magnetismo', 'Propriedade de atração de certos matteris, como ferro.'),
('Física', 'Óptica', 'Estudo do comportamento da luz e seus fenômenos.'),
('Física', 'Temperatura', 'Medida da energia térmica de um corpo.'),
('Física', 'Pressão', 'Força exercida por unidade de área.'),
('Física', 'Aceleração', 'Taxa de variação da velocidade em relação ao tempo.'),
('Física', 'Impulso', 'Produto da força aplicada sobre um corpo pelo tempo de aplicação.'),
('Física', 'Torque', 'Tendência de uma força causar rotação em um objeto.'),
('Física', 'Ondas', 'Perturbações que se propagam pelo espaço transportando energia.'),
('Física', 'Capacitor', 'Dispositivo que armazena energia elétrica em um campo elétrico.'),
('Biologia', 'Clorofila', 'Pigmento responsável pela absorção de luz na fotossíntese.'),
('Biologia', 'Ribossomo', 'Estrutura celular responsável pela síntese de proteínas.'),
('Biologia', 'Cadeia Alimentar', 'Sequência de transferência de energia entre organismos.'),
('Biologia', 'Biodiversidade', 'Variedade de espécies vivas em um ambiente.'),
('Biologia', 'Homeostase', 'Capacidade de um organismo de manter o equilíbrio interno.'),
('Biologia', 'Mutação', 'Alteração no matterl genético de um organismo.'),
('Biologia', 'Micróbio', 'Organismo microscópico, como bactérias e fungos.'),
('Biologia', 'Tecido', 'Grupo de células semelhantes que realizam funções específicas.'),
('Biologia', 'Hormônio', 'Substância química que regula funções biológicas.'),
('Biologia', 'Imunidade', 'Capacidade do organismo de resistir a infecções.'),
('História', 'Império', 'Estado ou grupo de territórios governados por um soberano.'),
('História', 'Ditadura', 'Regime político em que o poder é concentrado em uma pessoa ou grupo.'),
('História', 'Revolta', 'Ato de resistência ou oposição contra a autoridade estabelecida.'),
('História', 'Colonialismo', 'Sistema de dominação e exploração de territórios por nações estrangeiras.'),
('História', 'Iluminismo', 'Movimento intelectual que defendia razão e ciência no século XVIII.'),
('História', 'Cruzadas', 'Expedições religiosas e militares realizadas na Idade Média.'),
('História', 'Abolicionismo', 'Movimento para acabar com a escravidão.'),
('História', 'Primeira Guerra Mundial', 'Conflito global ocorrido entre 1914 e 1918.'),
('História', 'Segunda Guerra Mundial', 'Conflito global ocorrido entre 1939 e 1945.'),
('História', 'Reforma Protestante', 'Movimento religioso do século XVI que originou o protestantismo.'),
('Matemática', 'Derivada', 'Taxa de variação instantânea de uma função.'),
('Matemática', 'Integral', 'Medida da área sob a curva de uma função.'),
('Matemática', 'Teorema', 'Proposição matemática que pode ser demonstrada.'),
('Matemática', 'Vetor', 'Entidade com magnitude e direção.'),
('Matemática', 'Determinante', 'Valor que caracteriza uma matriz quadrada.'),
('Matemática', 'Números Primos', 'Números maiores que 1 divisíveis apenas por 1 e por si mesmos.'),
('Matemática', 'Equação Diferencial', 'Equação que envolve derivadas de uma função.'),
('Matemática', 'Progressão Aritmética', 'Sequência de números com diferença constante entre eles.'),
('Matemática', 'Progressão Geométrica', 'Sequência onde cada termo é o produto do anterior por uma constante.'),
('Matemática', 'Triângulo', 'Polígono de três lados e três ângulos.'),
('Filosofia', 'Ética', 'Estudo sobre os princípios que orientam a conduta humana.'),
('Filosofia', 'Estética', 'Ramo que estuda a beleza e a percepção artística.'),
('Filosofia', 'Metafísica', 'Ramo que investiga a natureza da realidade e da existência.'),
('Filosofia', 'Lógica', 'Estudo das regras e princípios do raciocínio válido.'),
('Filosofia', 'Epistemologia', 'Ramo que analisa o conhecimento e sua validade.'),
('Filosofia', 'Sofisma', 'Argumento falacioso usado para enganar ou persuadir.'),
('Filosofia', 'Dialética', 'Método de argumentação baseado na contradição e síntese.'),
('Filosofia', 'Determinismo', 'Doutrina que defende que eventos são determinados por causas anteriores.'),
('Filosofia', 'Existencialismo', 'Corrente filosófica que enfatiza a liberdade e a responsabilidade individual.'),
('Filosofia', 'Ceticismo', 'Doutrina que questiona a possibilidade de se alcançar o conhecimento.'),
('Sociologia', 'Estratificação Social', 'Divisão da sociedade em camadas hierárquicas.'),
('Sociologia', 'Cultura', 'Conjunto de costumes, valores e tradições de um grupo.'),
('Sociologia', 'Socialização', 'Processo de integração de um indivíduo em uma sociedade.'),
('Sociologia', 'Anomia', 'Ausência ou fragilidade de normas sociais em uma sociedade.'),
('Sociologia', 'Capital Social', 'Rede de relacionamentos que facilita ações coletivas.'),
('Sociologia', 'Etnocentrismo', 'Tendência de julgar outras culturas pela perspectiva da própria.'),
('Sociologia', 'Mobilidade Social', 'Capacidade de um indivíduo mudar de posição na hierarquia social.'),
('Sociologia', 'Estrutura Social', 'Organização estável das relações sociais em uma sociedade.'),
('Sociologia', 'Alienação', 'Fenômeno em que o indivíduo se sente desconectado da sociedade.'),
('Sociologia', 'Modernidade', 'Período histórico marcado pela industrialização e racionalização.'),
('Antropologia', 'Etnografia', 'Método de estudo detalhado das culturas humanas.'),
('Antropologia', 'Totemismo', 'Sistema de crenças que associa grupos humanos a símbolos naturais.'),
('Antropologia', 'Ritual', 'Conjunto de práticas simbólicas realizadas em contextos específicos.'),
('Antropologia', 'Cultura Material', 'Objetos físicos criados e utilizados por uma sociedade.'),
('Antropologia', 'Cosmologia', 'Sistema de crenças sobre a origem e a organização do universo.'),
('Antropologia', 'Tabu', 'Proibição cultural ou religiosa imposta a certas práticas ou temas.'),
('Antropologia', 'Patriarcalismo', 'Sistema social em que os homens possuem maior autoridade.'),
('Antropologia', 'Mito', 'Narrativa tradicional que explica fenômenos naturais e culturais.'),
('Antropologia', 'Simbolismo', 'Uso de símbolos para representar ideias ou conceitos abstratos.'),
('Antropologia', 'Parentesco', 'Relações sociais baseadas em laços familiares ou simbólicos.'),
('Filosofia', 'Hedonismo', 'Doutrina que identifica o prazer como o bem supremo da vida.'),
('Filosofia', 'Idealismo', 'Corrente que sustenta que a realidade é essencialmente mental ou espiritual.'),
('Filosofia', 'Materialismo', 'Doutrina que afirma que tudo o que existe é material ou físico.'),
('Filosofia', 'Utilitarismo', 'Teoria ética que define o correto como aquilo que maximiza o bem-estar coletivo.'),
('Filosofia', 'Pragmatismo', 'Corrente filosófica que enfatiza a utilidade prática do conhecimento.'),
('Filosofia', 'Existência', 'Estado ou condição de estar vivo ou ser real.'),
('Filosofia', 'Empirismo', 'Doutrina que considera a experiência sensorial como a base do conhecimento.'),
('Filosofia', 'Racionalismo', 'Corrente que prioriza a razão como fonte principal de conhecimento.'),
('Filosofia', 'Subjetivismo', 'Visão que afirma que a realidade depende da perspectiva do indivíduo.'),
('Filosofia', 'Objetivismo', 'Teoria que defende a existência de uma realidade independente da mente humana.'),
('Sociologia', 'Globalização', 'Processo de integração econômica, cultural e política em escala mundial.'),
('Sociologia', 'Individualismo', 'Ênfase na autonomia e independência do indivíduo em relação ao coletivo.'),
('Sociologia', 'Coesão Social', 'Grau de conexão e solidariedade entre os membros de uma sociedade.'),
('Sociologia', 'Instituição Social', 'Estrutura organizada que regula comportamentos e práticas sociais.'),
('Sociologia', 'Poder Simbólico', 'Capacidade de influenciar as percepções e crenças por meio de símbolos.'),
('Sociologia', 'Conflito Social', 'Disputa entre grupos com interesses divergentes em uma sociedade.'),
('Sociologia', 'Controle Social', 'Mecanismos que regulam o comportamento e mantêm a ordem social.'),
('Sociologia', 'Movimentos Sociais', 'Ações coletivas organizadas para promover mudanças sociais.'),
('Sociologia', 'Desigualdade Social', 'Diferenças nas condições de vida e acesso a recursos na sociedade.'),
('Antropologia', 'Acolhimento', 'Prática de integração e respeito a diferentes culturas e grupos.'),
('Antropologia', 'Hibridismo Cultural', 'Fusão de elementos de diferentes culturas para criar novas práticas ou valores.'),
('Antropologia', 'Relativismo Cultural', 'Visão que avalia práticas culturais dentro de seu próprio contexto.'),
('Antropologia', 'Linguagem', 'Sistema de comunicação simbólica usado por uma comunidade.'),
('Antropologia', 'Clãs', 'Grupos sociais baseados em laços de parentesco ou ancestralidade comum.'),
('Antropologia', 'Relações de Gênero', 'Interações sociais baseadas nas diferenças de gênero.'),
('Antropologia', 'Enculturação', 'Processo de aprendizado da cultura de um grupo.'),
('Antropologia', 'Xamanismo', 'Práticas espirituais que envolvem a comunicação com o mundo espiritual.'),
('Psicologia', 'Psicanálise', 'Método terapêutico e teoria sobre o funcionamento da mente.'),
('Psicologia', 'Comportamentalismo', 'Abordagem que estuda o comportamento observável.'),
('Psicologia', 'Cognição', 'Processos mentais envolvidos no conhecimento e compreensão.'),
('Psicologia', 'Empatia', 'Capacidade de compreender e compartilhar os sentimentos de outra pessoa.'),
('Psicologia', 'Personalidade', 'Conjunto de características que definem o comportamento de um indivíduo.'),
('Psicologia', 'Resiliência', 'Capacidade de superar adversidades e se adaptar.'),
('Psicologia', 'Neurose', 'Condição psicológica caracterizada por ansiedade ou conflitos emocionais.'),
('Psicologia', 'Psicopatia', 'Distúrbio de personalidade marcado por falta de empatia e comportamento antissocial.'),
('História', 'Feudalismo', 'Sistema político e econômico baseado em vínculos de vassalagem.'),
('História', 'Iluminismo', 'Movimento intelectual que valorizava a razão e a ciência.'),
('História', 'Revolução Industrial', 'Período de transformações tecnológicas e econômicas iniciado no século XVIII.'),
('História', 'Colonialismo', 'Dominação política e econômica de uma nação sobre outra.'),
('História', 'Nacionalismo', 'Ideologia que enfatiza a importância da nação e sua identidade.'),
('História', 'Imperialismo', 'Política de expansão territorial e domínio sobre outros povos.'),
('História', 'Renascimento', 'Movimento cultural que destacou a redescoberta da arte e do conhecimento clássico.'),
('História', 'Guerra Fria', 'Período de tensão geopolítica entre os blocos liderados pelos EUA e pela URSS.'),
('História', 'Revolução Francesa', 'Evento que marcou a transição do absolutismo para a democracia na França.'),
('História', 'Absolutismo', 'Forma de governo caracterizada pela centralização do poder no monarca.'),
('Ciência Política', 'Democracia', 'Sistema político em que o poder é exercido pelo povo, direta ou indiretamente.'),
('Ciência Política', 'Autoritarismo', 'Regime político marcado pela concentração de poder em um líder ou grupo.'),
('Ciência Política', 'Constitucionalismo', 'Doutrina que defende a limitação do poder governamental por meio de uma constituição.'),
('Ciência Política', 'Cidadania', 'Conjunto de direitos e deveres que vinculam o indivíduo ao Estado.'),
('Ciência Política', 'Soberania', 'Autoridade suprema de um Estado sobre seu território e população.'),
('Ciência Política', 'Parlamentarismo', 'Sistema de governo em que o parlamento tem papel central no poder executivo.'),
('Ciência Política', 'Totalitarismo', 'Regime político em que o Estado controla todos os aspectos da vida pública e privada.'),
('Ciência Política', 'Anarquia', 'Ausência de um governo ou autoridade central.'),
('Ciência Política', 'Política Pública', 'Conjunto de ações governamentais destinadas a resolver problemas sociais.'),
('Ciência Política', 'Geopolítica', 'Estudo das relações entre política e geografia.');

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
  ADD KEY `ID_U` (`ID_U`);

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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `attempts`
--
ALTER TABLE `attempts`
  MODIFY `ID_T` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `played`
--
ALTER TABLE `played`
  MODIFY `ID_PLAYED` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `rooms`
--
ALTER TABLE `rooms`
  MODIFY `ID_R` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `attempts_ibfk_1` FOREIGN KEY (`ID_ROUND`) REFERENCES `rounds` (`ID_ROUND`);

--
-- Restrições para tabelas `played`
--
ALTER TABLE `played`
  ADD CONSTRAINT `played_ibfk_1` FOREIGN KEY (`ID_U`) REFERENCES `users` (`ID_U`),
  ADD CONSTRAINT `played_ibfk_2` FOREIGN KEY (`ID_R`) REFERENCES `rooms` (`ID_R`);

--
-- Restrições para tabelas `ranking`
--
ALTER TABLE `ranking`
  ADD CONSTRAINT `ranking_ibfk_1` FOREIGN KEY (`ID_U`) REFERENCES `users` (`ID_U`);

--
-- Restrições para tabelas `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`ID_O`) REFERENCES `users` (`ID_U`);

--
-- Restrições para tabelas `rounds`
--
ALTER TABLE `rounds`
  ADD CONSTRAINT `rounds_ibfk_1` FOREIGN KEY (`ID_R`) REFERENCES `rooms` (`ID_R`),
  ADD CONSTRAINT `rounds_ibfk_2` FOREIGN KEY (`PLAYER_OF_THE_TIME`) REFERENCES `users` (`ID_U`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
