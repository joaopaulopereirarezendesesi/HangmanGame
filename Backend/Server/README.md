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

9. **Instale as dependências:**
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

## 5. Modelos de Dados

### Tabela `rooms`

| Campo      | Tipo    | Descrição             |
| ---------- | ------- | --------------------- |
| id         | INT     | Identificador da sala |
| name       | VARCHAR | Nome da sala          |
| maxPlayers | INT     | Máximo de jogadores   |

### Tabela `players`

| Campo  | Tipo    | Descrição                |
| ------ | ------- | ------------------------ |
| id     | INT     | Identificador do jogador |
| name   | VARCHAR | Nome do jogador          |
| roomId | INT     | Sala onde está conectado |

## 6. Autenticação e Autorização

O sistema suporta:

1. **JWT**: Para autenticação de jogadores logados.
2. **API Key**: Para integração com terceiros.
3. **Sessões WebSocket**: Cada jogador recebe um identificador único durante a conexão.

## 7. Logs e Monitoramento

- Logs de eventos são armazenados em `logs/server.log`.
- Erros são registrados no banco de dados na tabela `logs`.

## 8. Testes

Os testes podem ser executados via PHPUnit:

```bash
vendor/bin/phpunit
```

## 9. Exemplos de Uso

Para testar conexão WebSocket:

```js
const ws = new WebSocket("ws://seu-servidor.com/ws");
ws.onopen = () => {
  ws.send(
    JSON.stringify({ action: "join", roomId: "abc123", playerName: "Jogador1" })
  );
};
ws.onmessage = (event) => {
  console.log("Recebido:", event.data);
};
```

## 10. FAQ e Solução de Problemas

### O servidor WebSocket não inicia

- Verifique se a extensão `sockets` está habilitada no PHP.
- Confirme se nenhuma outra instância está rodando na porta configurada.

### Erro 500 ao criar sala

- Confira se o banco de dados está corretamente configurado no `.env`.

---

Essa documentação pode ser expandida conforme necessário. Se quiser adicionar mais detalhes, me avise!
