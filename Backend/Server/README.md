# 🕹️ Backend - Jogo da Forca

## 📌 Visão Geral

Este backend gerencia o CRUD e funcionalidades de um jogo da forca online.

**Tecnologias:**

- **PHP 8.2**
- **MySQL**
- **Apache (WebSockets via proxy)**
- **Composer + Bibliotecas:**
  - Imagick / ImageMagick
  - php-jwt (JWT)
  - phpmailer (e-mail)
  - phpdotenv (variáveis de ambiente)
  - twofactorauth (2FA)
  - bacon-qr-code (QR Code)
  - PDO (acesso ao BD)

---

## ⚙️ Instalação e Configuração

### ✅ Pré-requisitos

- PHP 8.2
- MySQL
- Composer
- Apache com suporte a WebSockets
- Imagick + ImageMagick

### 🛠️ Passo a passo

1. **Clone o projeto:**

   ```sh
   git clone https://github.com/joaopaulopereirarezendesesi/HangmanGame
   ```

2. **Instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html)** e adicione `C:\xampp\php` às variáveis de ambiente.

3. **Instale o [Composer](https://getcomposer.org/)**.

4. **Ative o OpenSSL:**

   - Edite `php.ini` e descomente `extension=openssl` e `extension=php_openssl.dll`.

5. **Configure o HTTPS:**

   - Siga este [tutorial](https://www.jetersonlordano.com.br/ferramentas-e-configuracoes/como-configurar-certificado-ssl-https-no-xampp-e-google-chrome).

6. **Ajuste o Apache:**

   - Edite `httpd.conf` e `httpd-ssl.conf`, atualizando `DocumentRoot` e `<Directory>` para o caminho da pasta `Backend/Server`.

7. **Crie o arquivo `.env`:**

   - Copie o conteúdo de `.env.exemple` e cole em um novo `.env`.

8. **Configure o banco de dados:**

   - Inicie o MySQL e Apache.
   - Importe o `.sql` da pasta `/DB/Backup`.
   - (Opcional) Popular com dados de `/DB/Pupular`.

9. **Instale o Imagick e o ImageMagick:**

- Baixe os arquivos ZIP do Imagick e do ImageMagick:

  - [Imagick](./assets/installer/php_imagick-3.7.0-8.2-ts-vs16-x64.zip)
  - [ImageMagick](./assets/installer/ImageMagick-7.1.0-18-vc15-x64.zip)

- Extraia os arquivos em uma nova pasta.

- Instale o Imagick:

  - Acesse a pasta extraída do Imagick (`php_imagick-3.7.0-8.2-ts-vs16-x64`).
  - Localize o arquivo `php_imagick.dll`.
  - Mova o arquivo `php_imagick.dll` para o diretório de extensões do PHP: `C:\xampp\php\ext`.

- Instale o ImageMagick:

  - Renomeie a pasta extraída do ImageMagick para `ImageMagick`.
  - Mova a pasta `ImageMagick` para o diretório raiz: `C:\`.

- Adicione uma variável de ambiente:

  - Crie uma variável de ambiente do tipo `Path` com o valor: `C:\ImageMagick`.

- Instale as dependências:

```sh
composer install
```

> ✅ Pronto! O backend está funcional.

---

## 🧱 Estrutura do Projeto

| Pasta          | Descrição                                     |
| -------------- | --------------------------------------------- |
| `assets/`      | Arquivos estáticos (imagens, CSS, zips, etc)  |
| `controllers/` | Handlers WebSocket e lógicas de controle      |
| `models/`      | Modelos de dados (Usuário, Sala, Partida)     |
| `config/`      | Arquivos de configuração geral                |
| `logs/`        | Logs gerados pelo servidor                    |
| `test/`        | Scripts de teste                              |
| `tools/`       | Ferramentas auxiliares                        |
| `core/`        | Inicialização e funções centrais da aplicação |
| `handlers/`    | Funções específicas e utilitárias             |

---

## 📡 Rotas da API

### 👤 `UserController`

- **POST `/User/index`** – Lista usuários
- **POST `/User/show`** – Exibe usuário por ID
- **POST `/User/create`** – Cria novo usuário (form-data)
- **POST `/User/login`** – Autentica usuário
- **GET `/User/getRoomOrganizer`** – Retorna salas criadas (JWT)
- **GET `/User/generateSecretImage`** – Gera QR Code 2FA

### 🏠 `RoomController`

- **POST `/Room/createRoom`** – Cria nova sala
- **POST `/Room/joinRoom`** – Entra em sala existente
- **POST `/Room/removePlayerFromRoom`** – Sai de uma sala
- **GET `/Room/getRooms`** – Lista salas disponíveis
- **POST `/Room/countPlayers`** – Conta jogadores na sala

### 🤝 `FriendsController`

- **GET `/Friends/getFriendsById`** – Lista amigos (JWT)

---

## 🧾 Modelos de Dados

### `users`

| Campo    | Tipo                              | Descrição                   |
| -------- | --------------------------------- | --------------------------- |
| ID_U     | CHAR(36)                          | ID do usuário (UUID)        |
| NICKNAME | VARCHAR(50)                       | Apelido do usuário          |
| EMAIL    | VARCHAR(100)                      | E-mail                      |
| PASSWORD | VARCHAR(255)                      | Senha criptografada         |
| ONLINE   | ENUM('offline', 'online', 'away') | Status atual do usuário     |
| PHOTO    | VARCHAR(255)                      | Caminho da imagem de perfil |
| TFA      | TINYINT(1)                        | 2FA ativado (1) ou não (0)  |

---

### `rooms`

| Campo           | Tipo         | Descrição                         |
| --------------- | ------------ | --------------------------------- |
| ID_R            | CHAR(36)     | ID da sala                        |
| ROOM_NAME       | VARCHAR(100) | Nome da sala                      |
| ID_O            | CHAR(36)     | ID do organizador (usuário)       |
| PRIVATE         | TINYINT(1)   | Sala privada (1) ou pública (0)   |
| PASSWORD        | VARCHAR(50)  | Senha da sala (se privada)        |
| PLAYER_CAPACITY | INT          | Capacidade máxima de jogadores    |
| TIME_LIMIT      | INT          | Tempo limite por rodada (minutos) |
| POINTS          | INT          | Pontuação total da sala           |
| MODALITY        | VARCHAR(255) | Nome da modalidade                |
| MODALITY_IMG    | VARCHAR(255) | Caminho da imagem da modalidade   |

---

### `rounds`

| Campo              | Tipo     | Descrição                                   |
| ------------------ | -------- | ------------------------------------------- |
| ID_RD              | CHAR(36) | ID da rodada                                |
| ID_R               | CHAR(36) | ID da sala                                  |
| PLAYER_OF_THE_TIME | CHAR(36) | Jogador da vez (usuário que define palavra) |

---

### `attempts`

| Campo      | Tipo         | Descrição                        |
| ---------- | ------------ | -------------------------------- |
| ID_T       | CHAR(36)     | ID da tentativa                  |
| ID_ROUND   | CHAR(36)     | ID da rodada                     |
| GUESS      | VARCHAR(255) | Letra/palavra tentada            |
| IS_CORRECT | TINYINT(1)   | Se a tentativa foi correta (1/0) |

---

### `played`

| Campo             | Tipo       | Descrição                       |
| ----------------- | ---------- | ------------------------------- |
| ID_PLAYED         | CHAR(36)   | ID da entrada de participação   |
| ID_U              | CHAR(36)   | ID do usuário                   |
| ID_R              | CHAR(36)   | ID da sala                      |
| SCORE             | INT        | Pontuação obtida                |
| IS_THE_CHALLENGER | TINYINT(1) | É o desafiante da rodada? (1/0) |

---

### `ranking`

| Campo           | Tipo     | Descrição                  |
| --------------- | -------- | -------------------------- |
| ID_U            | CHAR(36) | ID do usuário              |
| POSITION        | INT      | Posição no ranking         |
| AMOUNT_OF_WINS  | INT      | Total de vitórias          |
| NUMBER_OF_GAMES | INT      | Total de partidas jogadas  |
| POINT_AMOUNT    | INT      | Total de pontos acumulados |

---

### `wordsmatter`

| Campo      | Tipo         | Descrição                          |
| ---------- | ------------ | ---------------------------------- |
| ID_W       | CHAR(36)     | ID da palavra                      |
| MATTER     | VARCHAR(255) | Tema ou assunto relacionado        |
| WORD       | VARCHAR(255) | Palavra usada na rodada            |
| DEFINITION | TEXT         | Definição ou explicação da palavra |

---

### `photos`

| Campo   | Tipo         | Descrição                    |
| ------- | ------------ | ---------------------------- |
| ID_PH   | CHAR(36)     | ID da imagem                 |
| MATTER  | VARCHAR(255) | Tema associado à imagem      |
| ADDRESS | VARCHAR(255) | Caminho do arquivo da imagem |

---

### `friends`

| Campo | Tipo     | Descrição     |
| ----- | -------- | ------------- |
| ID_U  | CHAR(36) | ID do usuário |
| ID_A  | CHAR(36) | ID do amigo   |

---

### `friend_requests`

| Campo       | Tipo     | Descrição                    |
| ----------- | -------- | ---------------------------- |
| ID          | CHAR(36) | ID da solicitação            |
| SENDER_ID   | CHAR(36) | Usuário que enviou o pedido  |
| RECEIVER_ID | CHAR(36) | Usuário que recebeu o pedido |

---

### `codestwofa`

| Campo   | Tipo     | Descrição                      |
| ------- | -------- | ------------------------------ |
| ID_CTFA | CHAR(36) | ID do código 2FA               |
| ID_U    | CHAR(36) | ID do usuário                  |
| CODE    | INT(11)  | Código gerado para verificação |

---

## 6. Autenticação e Autorização

O sistema suporta:

1. **JWT**: Para autenticação de jogadores logados.

Essa documentação pode ser expandida conforme necessário. Se quiser adicionar mais detalhes, me avise!
