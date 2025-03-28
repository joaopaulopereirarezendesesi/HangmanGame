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
