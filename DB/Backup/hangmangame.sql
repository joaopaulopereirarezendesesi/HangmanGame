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
(UUID(), 'livre', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/livre.png'),
(UUID(), 'antropologia', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/antropologia.png'),
(UUID(), 'biologia', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/biologia.png'),
(UUID(), 'cienciapolitica', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/cienciapolitica.png'),
(UUID(), 'filosofia', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/filosofia.png'),
(UUID(), 'fisica', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/fisica.png'),
(UUID(), 'geografia', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/geografia.png'),
(UUID(), 'historia', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/historia.png'),
(UUID(), 'matematica', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/matematica.png'),
(UUID(), 'psicologia', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/psicologia.png'),
(UUID(), 'sociologia', 'http://localhost:80/assets/photos/sysPhotos/roomPhotos/sociologia.png');

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

iNSERT INTO `wordsmatter` (`ID_W`, `matter`, `word`, `definition`) VALUES
(UUID(), 'Geografia', 'Continente', 'Grande massa de terra delimitada pelos oceanos.'),
(UUID(), 'Geografia', 'Latitude', 'Distância angular de um ponto em relação ao Equador.'),
(UUID(), 'Geografia', 'Longitude', 'Distância angular de um ponto em relação ao Meridiano de Greenwich.'),
(UUID(), 'Geografia', 'Relevo', 'Formas da superfície terrestre, como montanhas e planícies.'),
(UUID(), 'Geografia', 'Bioma', 'Grande conjunto de ecossistemas com características semelhantes.'),
(UUID(), 'Geografia', 'Tectônica de Placas', 'Teoria que explica o movimento das placas tectônicas da Terra.'),
(UUID(), 'Geografia', 'Erosão', 'Desgaste das rochas e do solo por agentes naturais.'),
(UUID(), 'Geografia', 'Clima', 'Conjunto de condições atmosféricas de uma região por um longo período.'),
(UUID(), 'Geografia', 'Vegetação', 'Conjunto de plantas que crescem naturalmente em uma região.'),
(UUID(), 'Geografia', 'Hidrografia', 'Estudo das águas na superfície terrestre.'),
(UUID(), 'Física', 'Força', 'Interação que muda o estado de movimento de um objeto.'),
(UUID(), 'Física', 'Energia', 'Capacidade de realizar trabalho ou produzir movimento.'),
(UUID(), 'Física', 'Movimento', 'Mudança de posição de um corpo ao longo do tempo.'),
(UUID(), 'Física', 'Velocidade', 'Relação entre o deslocamento e o tempo.'),
(UUID(), 'Física', 'Gravidade', 'Força que atrai corpos para o centro da Terra.'),
(UUID(), 'Física', 'Massa', 'Quantidade de matéria em um corpo.'),
(UUID(), 'Física', 'Trabalho', 'Energia transferida para realizar um deslocamento.'),
(UUID(), 'Física', 'Fricção', 'Força que resiste ao movimento relativo entre superfícies.'),
(UUID(), 'Física', 'Potência', 'Taxa de realização de trabalho ou consumo de energia.'),
(UUID(), 'Física', 'Inércia', 'Tendência de um corpo de manter seu estado de movimento.'),
(UUID(), 'Biologia', 'Fotossíntese', 'Processo pelo qual plantas convertem luz em energia química.'),
(UUID(), 'Biologia', 'Célula', 'Unidade básica da vida dos organismos vivos.'),
(UUID(), 'Biologia', 'Mitose', 'Processo de divisão celular que resulta em duas células idênticas.'),
(UUID(), 'Biologia', 'Ecossistema', 'Conjunto de organismos vivos interagindo com o ambiente.'),
(UUID(), 'Biologia', 'Gene', 'Unidade de hereditariedade que determina características dos organismos.'),
(UUID(), 'Biologia', 'DNA', 'Molécula que contém informações genéticas dos seres vivos.'),
(UUID(), 'Biologia', 'Habitat', 'Ambiente natural onde vive um organismo.'),
(UUID(), 'Biologia', 'Respiração celular', 'Processo de produção de energia em células.'),
(UUID(), 'Biologia', 'Organismo', 'Qualquer ser vivo, como animais, plantas e microrganismos.'),
(UUID(), 'Biologia', 'Enzima', 'Molécula que acelera reações químicas nos organismos vivos.'),
(UUID(), 'História', 'Civilização', 'Sociedade com cultura e organização complexas.'),
(UUID(), 'História', 'Revolução', 'Mudança drástica e significativa em uma sociedade.'),
(UUID(), 'História', 'Monarquia', 'Forma de governo liderada por um rei ou rainha.'),
(UUID(), 'História', 'Democracia', 'Sistema político onde o poder é exercido pelo povo.'),
(UUID(), 'História', 'Colonização', 'Processo de ocupação e exploração de um território por outro país.'),
(UUID(), 'História', 'Escravidão', 'Sistema onde pessoas são propriedade de outras.'),
(UUID(), 'História', 'Renascimento', 'Movimento cultural e artístico da Europa nos séculos XIV-XVI.'),
(UUID(), 'História', 'Guerra Fria', 'Conflito ideológico entre EUA e URSS no século XX.'),
(UUID(), 'História', 'Feudalismo', 'Sistema econômico e social baseado em terras e servidão.'),
(UUID(), 'História', 'Revolução Industrial', 'Transição para processos de produção mecanizados.'),
(UUID(), 'Matemática', 'Equação', 'Expressão matemática que afirma a igualdade entre duas partes.'),
(UUID(), 'Matemática', 'Função', 'Relação entre um conjunto de entradas e saídas.'),
(UUID(), 'Matemática', 'Geometria', 'Estudo das formas, tamanhos e propriedades do espaço.'),
(UUID(), 'Matemática', 'Álgebra', 'Ramo da matemática que usa símbolos para resolver problemas.'),
(UUID(), 'Matemática', 'Cálculo', 'Estudo das taxas de variação e acumulação.'),
(UUID(), 'Matemática', 'Probabilidade', 'Medida da chance de um evento ocorrer.'),
(UUID(), 'Matemática', 'Estatística', 'Ramo que coleta, analisa e interpreta dados.'),
(UUID(), 'Matemática', 'Logaritmo', 'Inverso da exponenciação, usado em cálculos avançados.'),
(UUID(), 'Matemática', 'Matriz', 'Tabela de números organizados em linhas e colunas.'),
(UUID(), 'Matemática', 'Hipotenusa', 'Maior lado de um triângulo retângulo.'),
(UUID(), 'Geografia', 'Clima', 'Condições atmosféricas típicas de uma região ao longo do tempo.'),
(UUID(), 'Geografia', 'Mapa', 'Representação gráfica de uma área geográfica.'),
(UUID(), 'Geografia', 'Cartografia', 'Ciência de criar mapas e representações da Terra.'),
(UUID(), 'Geografia', 'Oceano', 'Grande massa de água salgada que cobre a maior parte da superfície terrestre.'),
(UUID(), 'Geografia', 'Deserto', 'Região com pouca precipitação e vegetação escassa.'),
(UUID(), 'Geografia', 'Floresta', 'Área densa de árvores e vegetação variada.'),
(UUID(), 'Geografia', 'Urbanização', 'Processo de crescimento e desenvolvimento das cidades.'),
(UUID(), 'Geografia', 'Globalização', 'Integração econômica, social e cultural entre países.'),
(UUID(), 'Geografia', 'Savana', 'Bioma tropical caracterizado por gramíneas e árvores espaçadas.'),
(UUID(), 'Geografia', 'Região', 'Área delimitada com características geográficas ou culturais comuns.'),
(UUID(), 'Física', 'Eletricidade', 'Fenômeno relacionado ao fluxo de cargas elétricas.'),
(UUID(), 'Física', 'Magnetismo', 'Propriedade de atração de certos matteris, como ferro.'),
(UUID(), 'Física', 'Óptica', 'Estudo do comportamento da luz e seus fenômenos.'),
(UUID(), 'Física', 'Temperatura', 'Medida da energia térmica de um corpo.'),
(UUID(), 'Física', 'Pressão', 'Força exercida por unidade de área.'),
(UUID(), 'Física', 'Aceleração', 'Taxa de variação da velocidade em relação ao tempo.'),
(UUID(), 'Física', 'Impulso', 'Produto da força aplicada sobre um corpo pelo tempo de aplicação.'),
(UUID(), 'Física', 'Torque', 'Tendência de uma força causar rotação em um objeto.'),
(UUID(), 'Física', 'Ondas', 'Perturbações que se propagam pelo espaço transportando energia.'),
(UUID(), 'Física', 'Capacitor', 'Dispositivo que armazena energia elétrica em um campo elétrico.'),
(UUID(), 'Biologia', 'Clorofila', 'Pigmento responsável pela absorção de luz na fotossíntese.'),
(UUID(), 'Biologia', 'Ribossomo', 'Estrutura celular responsável pela síntese de proteínas.'),
(UUID(), 'Biologia', 'Cadeia Alimentar', 'Sequência de transferência de energia entre organismos.'),
(UUID(), 'Biologia', 'Biodiversidade', 'Variedade de espécies vivas em um ambiente.'),
(UUID(), 'Biologia', 'Homeostase', 'Capacidade de um organismo de manter o equilíbrio interno.'),
(UUID(), 'Biologia', 'Mutação', 'Alteração no matterl genético de um organismo.'),
(UUID(), 'Biologia', 'Micróbio', 'Organismo microscópico, como bactérias e fungos.'),
(UUID(), 'Biologia', 'Tecido', 'Grupo de células semelhantes que realizam funções específicas.'),
(UUID(), 'Biologia', 'Hormônio', 'Substância química que regula funções biológicas.'),
(UUID(), 'Biologia', 'Imunidade', 'Capacidade do organismo de resistir a infecções.'),
(UUID(), 'História', 'Império', 'Estado ou grupo de territórios governados por um soberano.'),
(UUID(), 'História', 'Ditadura', 'Regime político em que o poder é concentrado em uma pessoa ou grupo.'),
(UUID(), 'História', 'Revolta', 'Ato de resistência ou oposição contra a autoridade estabelecida.'),
(UUID(), 'História', 'Colonialismo', 'Sistema de dominação e exploração de territórios por nações estrangeiras.'),
(UUID(), 'História', 'Iluminismo', 'Movimento intelectual que defendia razão e ciência no século XVIII.'),
(UUID(), 'História', 'Cruzadas', 'Expedições religiosas e militares realizadas na Idade Média.'),
(UUID(), 'História', 'Abolicionismo', 'Movimento para acabar com a escravidão.'),
(UUID(), 'História', 'Primeira Guerra Mundial', 'Conflito global ocorrido entre 1914 e 1918.'),
(UUID(), 'História', 'Segunda Guerra Mundial', 'Conflito global ocorrido entre 1939 e 1945.'),
(UUID(), 'História', 'Reforma Protestante', 'Movimento religioso do século XVI que originou o protestantismo.'),
(UUID(), 'Matemática', 'Derivada', 'Taxa de variação instantânea de uma função.'),
(UUID(), 'Matemática', 'Integral', 'Medida da área sob a curva de uma função.'),
(UUID(), 'Matemática', 'Teorema', 'Proposição matemática que pode ser demonstrada.'),
(UUID(), 'Matemática', 'Vetor', 'Entidade com magnitude e direção.'),
(UUID(), 'Matemática', 'Determinante', 'Valor que caracteriza uma matriz quadrada.'),
(UUID(), 'Matemática', 'Números Primos', 'Números maiores que 1 divisíveis apenas por 1 e por si mesmos.'),
(UUID(), 'Matemática', 'Equação Diferencial', 'Equação que envolve derivadas de uma função.'),
(UUID(), 'Matemática', 'Progressão Aritmética', 'Sequência de números com diferença constante entre eles.'),
(UUID(), 'Matemática', 'Progressão Geométrica', 'Sequência onde cada termo é o produto do anterior por uma constante.'),
(UUID(), 'Matemática', 'Triângulo', 'Polígono de três lados e três ângulos.'),
(UUID(), 'Filosofia', 'Ética', 'Estudo sobre os princípios que orientam a conduta humana.'),
(UUID(), 'Filosofia', 'Estética', 'Ramo que estuda a beleza e a percepção artística.'),
(UUID(), 'Filosofia', 'Metafísica', 'Ramo que investiga a natureza da realidade e da existência.'),
(UUID(), 'Filosofia', 'Lógica', 'Estudo das regras e princípios do raciocínio válido.'),
(UUID(), 'Filosofia', 'Epistemologia', 'Ramo que analisa o conhecimento e sua validade.'),
(UUID(), 'Filosofia', 'Sofisma', 'Argumento falacioso usado para enganar ou persuadir.'),
(UUID(), 'Filosofia', 'Dialética', 'Método de argumentação baseado na contradição e síntese.'),
(UUID(), 'Filosofia', 'Determinismo', 'Doutrina que defende que eventos são determinados por causas anteriores.'),
(UUID(), 'Filosofia', 'Existencialismo', 'Corrente filosófica que enfatiza a liberdade e a responsabilidade individual.'),
(UUID(), 'Filosofia', 'Ceticismo', 'Doutrina que questiona a possibilidade de se alcançar o conhecimento.'),
(UUID(), 'Sociologia', 'Estratificação Social', 'Divisão da sociedade em camadas hierárquicas.'),
(UUID(), 'Sociologia', 'Cultura', 'Conjunto de costumes, valores e tradições de um grupo.'),
(UUID(), 'Sociologia', 'Socialização', 'Processo de integração de um indivíduo em uma sociedade.'),
(UUID(), 'Sociologia', 'Anomia', 'Ausência ou fragilidade de normas sociais em uma sociedade.'),
(UUID(), 'Sociologia', 'Capital Social', 'Rede de relacionamentos que facilita ações coletivas.'),
(UUID(), 'Sociologia', 'Etnocentrismo', 'Tendência de julgar outras culturas pela perspectiva da própria.'),
(UUID(), 'Sociologia', 'Mobilidade Social', 'Capacidade de um indivíduo mudar de posição na hierarquia social.'),
(UUID(), 'Sociologia', 'Estrutura Social', 'Organização estável das relações sociais em uma sociedade.'),
(UUID(), 'Sociologia', 'Alienação', 'Fenômeno em que o indivíduo se sente desconectado da sociedade.'),
(UUID(), 'Sociologia', 'Modernidade', 'Período histórico marcado pela industrialização e racionalização.'),
(UUID(), 'Antropologia', 'Etnografia', 'Método de estudo detalhado das culturas humanas.'),
(UUID(), 'Antropologia', 'Totemismo', 'Sistema de crenças que associa grupos humanos a símbolos naturais.'),
(UUID(), 'Antropologia', 'Ritual', 'Conjunto de práticas simbólicas realizadas em contextos específicos.'),
(UUID(), 'Antropologia', 'Cultura Material', 'Objetos físicos criados e utilizados por uma sociedade.'),
(UUID(), 'Antropologia', 'Cosmologia', 'Sistema de crenças sobre a origem e a organização do universo.'),
(UUID(), 'Antropologia', 'Tabu', 'Proibição cultural ou religiosa imposta a certas práticas ou temas.'),
(UUID(), 'Antropologia', 'Patriarcalismo', 'Sistema social em que os homens possuem maior autoridade.'),
(UUID(), 'Antropologia', 'Mito', 'Narrativa tradicional que explica fenômenos naturais e culturais.'),
(UUID(), 'Antropologia', 'Simbolismo', 'Uso de símbolos para representar ideias ou conceitos abstratos.'),
(UUID(), 'Antropologia', 'Parentesco', 'Relações sociais baseadas em laços familiares ou simbólicos.'),
(UUID(), 'Filosofia', 'Hedonismo', 'Doutrina que identifica o prazer como o bem supremo da vida.'),
(UUID(), 'Filosofia', 'Idealismo', 'Corrente que sustenta que a realidade é essencialmente mental ou espiritual.'),
(UUID(), 'Filosofia', 'Materialismo', 'Doutrina que afirma que tudo o que existe é material ou físico.'),
(UUID(), 'Filosofia', 'Utilitarismo', 'Teoria ética que define o correto como aquilo que maximiza o bem-estar coletivo.'),
(UUID(), 'Filosofia', 'Pragmatismo', 'Corrente filosófica que enfatiza a utilidade prática do conhecimento.'),
(UUID(), 'Filosofia', 'Existência', 'Estado ou condição de estar vivo ou ser real.'),
(UUID(), 'Filosofia', 'Empirismo', 'Doutrina que considera a experiência sensorial como a base do conhecimento.'),
(UUID(), 'Filosofia', 'Racionalismo', 'Corrente que prioriza a razão como fonte principal de conhecimento.'),
(UUID(), 'Filosofia', 'Subjetivismo', 'Visão que afirma que a realidade depende da perspectiva do indivíduo.'),
(UUID(), 'Filosofia', 'Objetivismo', 'Teoria que defende a existência de uma realidade independente da mente humana.'),
(UUID(), 'Sociologia', 'Globalização', 'Processo de integração econômica, cultural e política em escala mundial.'),
(UUID(), 'Sociologia', 'Individualismo', 'Ênfase na autonomia e independência do indivíduo em relação ao coletivo.'),
(UUID(), 'Sociologia', 'Coesão Social', 'Grau de conexão e solidariedade entre os membros de uma sociedade.'),
(UUID(), 'Sociologia', 'Instituição Social', 'Estrutura organizada que regula comportamentos e práticas sociais.'),
(UUID(), 'Sociologia', 'Poder Simbólico', 'Capacidade de influenciar as percepções e crenças por meio de símbolos.'),
(UUID(), 'Sociologia', 'Conflito Social', 'Disputa entre grupos com interesses divergentes em uma sociedade.'),
(UUID(), 'Sociologia', 'Controle Social', 'Mecanismos que regulam o comportamento e mantêm a ordem social.'),
(UUID(), 'Sociologia', 'Movimentos Sociais', 'Ações coletivas organizadas para promover mudanças sociais.'),
(UUID(), 'Sociologia', 'Desigualdade Social', 'Diferenças nas condições de vida e acesso a recursos na sociedade.'),
(UUID(), 'Antropologia', 'Acolhimento', 'Prática de integração e respeito a diferentes culturas e grupos.'),
(UUID(), 'Antropologia', 'Hibridismo Cultural', 'Fusão de elementos de diferentes culturas para criar novas práticas ou valores.'),
(UUID(), 'Antropologia', 'Relativismo Cultural', 'Visão que avalia práticas culturais dentro de seu próprio contexto.'),
(UUID(), 'Antropologia', 'Linguagem', 'Sistema de comunicação simbólica usado por uma comunidade.'),
(UUID(), 'Antropologia', 'Clãs', 'Grupos sociais baseados em laços de parentesco ou ancestralidade comum.'),
(UUID(), 'Antropologia', 'Relações de Gênero', 'Interações sociais baseadas nas diferenças de gênero.'),
(UUID(), 'Antropologia', 'Enculturação', 'Processo de aprendizado da cultura de um grupo.'),
(UUID(), 'Antropologia', 'Xamanismo', 'Práticas espirituais que envolvem a comunicação com o mundo espiritual.'),
(UUID(), 'Psicologia', 'Psicanálise', 'Método terapêutico e teoria sobre o funcionamento da mente.'),
(UUID(), 'Psicologia', 'Comportamentalismo', 'Abordagem que estuda o comportamento observável.'),
(UUID(), 'Psicologia', 'Cognição', 'Processos mentais envolvidos no conhecimento e compreensão.'),
(UUID(), 'Psicologia', 'Empatia', 'Capacidade de compreender e compartilhar os sentimentos de outra pessoa.'),
(UUID(), 'Psicologia', 'Personalidade', 'Conjunto de características que definem o comportamento de um indivíduo.'),
(UUID(), 'Psicologia', 'Resiliência', 'Capacidade de superar adversidades e se adaptar.'),
(UUID(), 'Psicologia', 'Neurose', 'Condição psicológica caracterizada por ansiedade ou conflitos emocionais.'),
(UUID(), 'Psicologia', 'Psicopatia', 'Distúrbio de personalidade marcado por falta de empatia e comportamento antissocial.'),
(UUID(), 'História', 'Feudalismo', 'Sistema político e econômico baseado em vínculos de vassalagem.'),
(UUID(), 'História', 'Iluminismo', 'Movimento intelectual que valorizava a razão e a ciência.'),
(UUID(), 'História', 'Revolução Industrial', 'Período de transformações tecnológicas e econômicas iniciado no século XVIII.'),
(UUID(), 'História', 'Colonialismo', 'Dominação política e econômica de uma nação sobre outra.'),
(UUID(), 'História', 'Nacionalismo', 'Ideologia que enfatiza a importância da nação e sua identidade.'),
(UUID(), 'História', 'Imperialismo', 'Política de expansão territorial e domínio sobre outros povos.'),
(UUID(), 'História', 'Renascimento', 'Movimento cultural que destacou a redescoberta da arte e do conhecimento clássico.'),
(UUID(), 'História', 'Guerra Fria', 'Período de tensão geopolítica entre os blocos liderados pelos EUA e pela URSS.'),
(UUID(), 'História', 'Revolução Francesa', 'Evento que marcou a transição do absolutismo para a democracia na França.'),
(UUID(), 'História', 'Absolutismo', 'Forma de governo caracterizada pela centralização do poder no monarca.'),
(UUID(), 'Ciência Política', 'Democracia', 'Sistema político em que o poder é exercido pelo povo, direta ou indiretamente.'),
(UUID(), 'Ciência Política', 'Autoritarismo', 'Regime político marcado pela concentração de poder em um líder ou grupo.'),
(UUID(), 'Ciência Política', 'Constitucionalismo', 'Doutrina que defende a limitação do poder governamental por meio de uma constituição.'),
(UUID(), 'Ciência Política', 'Cidadania', 'Conjunto de direitos e deveres que vinculam o indivíduo ao Estado.'),
(UUID(), 'Ciência Política', 'Soberania', 'Autoridade suprema de um Estado sobre seu território e população.'),
(UUID(), 'Ciência Política', 'Parlamentarismo', 'Sistema de governo em que o parlamento tem papel central no poder executivo.'),
(UUID(), 'Ciência Política', 'Totalitarismo', 'Regime político em que o Estado controla todos os aspectos da vida pública e privada.'),
(UUID(), 'Ciência Política', 'Anarquia', 'Ausência de um governo ou autoridade central.'),
(UUID(), 'Ciência Política', 'Política Pública', 'Conjunto de ações governamentais destinadas a resolver problemas sociais.'),
(UUID(), 'Ciência Política', 'Geopolítica', 'Estudo das relações entre política e geografia.');

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
