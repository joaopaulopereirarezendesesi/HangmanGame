# Documentação do Backend - Jogo da Forca

## 1. Visão Geral

Este backend gerencia o CRUD e funcionalidade de um Jogo da Forca online.

**Tecnologias utilizadas:**

- PHP 8.2
- MySQL (armazenamento de dados)
- Apache (servidor HTTP com suporte a WebSockets via proxy)
- Imagick (Biblioteca de criação de imagens)
- php-jwt (Biblioteca de integração de tokens JWT)
- phpmailer (Biblioteca de integração de envio de emails)
- phpdotenv (Biblioteca para carregamento de variáveis de ambiente do .env)
- twofactorauth (Biblioteca para adicionar autenticação de dois fatores)
- bacon-qr-code (Biblioteca para geração de códigos QR)
- PDO (Dependência do PDO (PHP Data Objects))

## 2. Guia de Instalação e Configuração

### Requisitos do Sistema

- PHP 8.2
- MySQL
- Composer
- Apache com suporte a WebSockets
- Imagick
- ImageMagick

## 🔧 Executando o Backend (para testes)

1. **Clone o repositório:**
   ```sh
   git clone https://github.com/joaopaulopereirarezendesesi/HangmanGame
   ```
2. **Instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html)** e adicione `C:\xampp\php` às variáveis de ambiente do sistema.

3. **Instale o gerneciador de dependências [Composer](https://getcomposer.org/)**

4. **Habilite OpenSSL no Apache:**

   - No XAMPP, clique em **Config** do Apache e edite `php.ini`.
   - Localize `openssl` e descomente `;extension=openssl` e `;extension=php_openssl.dll` retirando o `;`.

5. **Gere certificado HTTPS:**

   - [Blog de tutorial](https://www.jetersonlordano.com.br/ferramentas-e-configuracoes/como-configurar-certificado-ssl-https-no-xampp-e-google-chrome)

6. **Configure a pasta root do Apache:**

   - Edite `httpd.conf` e `httpd-ssl.conf`(você pode localizar esses arquivos clicando no botão `config` do apache na interface do XAMPP), alterando `DocumentRoot`(nos dois arquivos) e `<Directory "C:/xampp/htdocs">` (no arquivo `httpd.conf`) para o caminho do Backend/Server deste projeto.
   - Reinicie o Apache.

7. **Crie o arquivo `.env` na pasta Backend/Server:**

- Vá até `.env.exemple` e copie o conteudo deste arquivo para um arquivo `.env` na raiz do projeto

8. **Configure o banco de dados:**

   - Inicie os serviços do MySQL e Apache no XAMPP.
   - Crie um banco e importe o banco disponível na pasta `/DB/Backup`.
     **Para testes:**
   - Pupule o BD com o arquivo de INSERTS `/DB/Pupular`

9. **Instale as dependências do backend:**

- Instale Imagick (biblioteca de criação de imagens):

  - [Imagick](./assets/installer/php_imagick-3.7.0-8.2-ts-vs16-x64.zip)

- Instalando dependencias:

```sh
composer install
```

✅ **Backend pronto para uso!**

## 3. Arquitetura do Sistema

- **`assets/`**: Contém arquivos estáticos, como imagens, estilos e arquivos compactados.
- **`controllers/`**: Contém os handlers de conexões WebSocket, responsáveis por gerenciar eventos e interações.
- **`models/`**: Contém os modelos de dados, como jogadores, salas e partidas.
- **`config/`**: Armazena configurações gerais do sistema.
- **`logs/`**: Diretório onde são armazenados os arquivos de log do servidor.
- **`test/`**: Arquivo de testes de funcionalidades.
- **`tools/`**: Reúne ferramentas auxiliares utilizadas no desenvolvimento e manutenção do sistema.
- **`core/`**: Contém o núcleo da aplicação, incluindo a inicialização e funções essenciais.
- **`handlers/`**: Pasta que tem classes com funções expecificas.

## 4. Rotas da API

- UserController

### POST User/index

**Descrição:** Lista todos os usuários.

**Requisição:**

```json
{}
```

**Resposta de sucesso:**

```json
[
  {
    "ID_U": 1,
    "NICKNAME": "fulano",
    "EMAIL": "fulano@email.com",
    "PHOTO": "http://localhost:80/assets/photos/usersPhotos/fulano.png"
  }
]
```

**Resposta de erro:**

```json
{
  "error": "Internal server error"
}
```

---

### POST User/show

**Descrição:** Exibe os dados de um usuário pelo ID.

**Requisição:**

```json
{
  "id": 1
}
```

**Resposta de sucesso:**

```json
{
  "ID_U": 1,
  "NICKNAME": "fulano",
  "EMAIL": "fulano@email.com",
  "PHOTO": "http://localhost:80/assets/photos/usersPhotos/fulano.png"
}
```

**Resposta de erro:**

```json
{
  "error": "User not found"
}
```

---

### POST User/create

**Descrição:** Cria um novo usuário. (_multipart/form-data_)

**Requisição (form-data):**

- nickname: string
- email: string
- password: string
- confirm_password: string
- profileImage: arquivo .png/.jpg

**Resposta de sucesso:**

```json
{
  "message": "User successfully created!"
}
```

**Resposta de erro:**

```json
{
  "error": "Passwords do not match"
}
```

---

### POST User/login

**Descrição:** Login do usuário com email e senha.

**Requisição:**

```json
{
  "email": "fulano@email.com",
  "password": "Senha@123"
}
```

**Resposta de sucesso:**

```json
{
  "message": "Login successful"
}
```

**Cookies definidos:**

- jwt (criptografado)
- nickname
- photo

**Resposta de erro:**

```json
{
  "error": "Invalid credentials"
}
```

---

### GET User/getRoomOrganizer

**Descrição:** Retorna as salas organizadas pelo usuário logado (via token JWT).

**Requisição:** _(sem corpo)_

**Resposta de sucesso:**

```json
{
  "rooms": [
    {
      "ID_ROOM": 1,
      "NAME": "Sala A",
      "DESCRIPTION": "Descrição aqui"
    }
  ]
}
```

**Resposta de erro:**

```json
{
  "error": "Token not provided"
}
```

---

### GET User/generateSecretImage

**Descrição:** Gera imagem/QR code para 2FA do usuário autenticado.

**Requisição:** _(sem corpo, requer JWT no cookie)_

**Resposta esperada:** Imagem inline ou download automático

**Resposta de erro:**

```json
{
  "error": "Token not provided"
}
```

- RoomController

### POST Room/createRoom

**Descrição:** Cria uma nova sala.

**Requisição (form-data):**

- room_name: string (opcional)
- private: boolean
- password: string (necessário se `private` for `true`)
- player_capacity: int (mínimo 2, padrão 10)
- time_limit: int (mínimo 2, padrão 5)
- points: int (padrão 2000)
- modality: string (ex: "criptografia")

**Resposta de sucesso:**

```json
{
  "id_creator": 1,
  "id_room": 10,
  "room_name": "Minha Sala",
  "private": true,
  "capacity": 5,
  "timeout": 5,
  "points": 2000,
  "modality": "criptografia"
}
```

**Resposta de erro:**

```json
{
  "error": "Room name already in use."
}
```

ou

```json
{
  "error": "Password required for private rooms."
}
```

ou

```json
{
  "error": "Invalid capacity or time limit."
}
```

---

### POST Room/joinRoom

**Descrição:** Permite que um usuário entre em uma sala existente.

**Requisição:**

```json
{
  "roomId": 10,
  "password": "1234" // somente se a sala for privada
}
```

**Resposta de sucesso:**

```json
{
  "message": "Successfully joined the room."
}
```

**Resposta de erro:**

```json
{
  "error": "Room not found."
}
```

ou

```json
{
  "error": "Invalid password."
}
```

ou

```json
{
  "error": "Room is full."
}
```

---

### POST Room/removePlayerFromRoom

**Descrição:** Remove o jogador logado da sala informada.

**Requisição:**

```json
{
  "roomId": 10
}
```

**Resposta de sucesso:**

```json
{
  "message": "Player successfully removed."
}
```

**Resposta de erro:**

```json
{
  "error": "Room not found."
}
```

---

### GET Room/getRooms

**Descrição:** Lista todas as salas disponíveis.

**Requisição:** _(sem corpo, precisa estar autenticado)_

**Resposta de sucesso:**

```json
{
  "rooms": [
    {
      "ID_ROOM": 1,
      "NAME": "Sala A",
      "PRIVATE": false,
      "PLAYER_CAPACITY": 10,
      ...
    }
  ]
}
```

**Resposta de erro:**

```json
{
  "message": "No rooms found"
}
```

---

### POST Room/countPlayers

**Descrição:** Conta quantos jogadores estão em uma sala.

**Requisição:**

```json
{
  "roomId": 10
}
```

**Resposta de sucesso:**

```json
{
  "players": 4
}
```

**Resposta de erro:**

```json
{
  "error": "Room ID not provided"
}
```

- FriendsController

### GET Friends/getFriendsById

**Descrição:** Retorna a lista de amigos do usuário autenticado.

**Requisição:** _(necessário token JWT no cabeçalho Authorization)_

**Headers:**

```
Authorization: Bearer <seu_token>
```

**Resposta de sucesso:**

```json
{
  "friends": [
    {
      "id": "123",
      "username": "amigo1",
      "status": "online"
    },
    {
      "id": "456",
      "username": "amigo2",
      "status": "offline"
    }
  ]
}
```

**Resposta de erro (sem token):**

```json
{
  "error": "Token not provided"
}
```

**Resposta de erro (servidor):**

```json
{
  "error": "Internal server error"
}
```

**Observações:**

- A função depende de autenticação via token.
- O método chama `getFriendsById` no modelo para buscar a lista.

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
