# Documentação do Backend - Jogo da Forca

## 1. Visão Geral
Este backend gerencia um jogo da forca multiplayer em tempo real, utilizando WebSockets para comunicação entre jogadores.

**Tecnologias utilizadas:**
- PHP 8.2
- Ratchet (biblioteca WebSocket para PHP)
- ReactPHP (para eventos assíncronos)
- MySQL (armazenamento de dados)
- Apache (servidor HTTP com suporte a WebSockets via proxy)

## 2. Guia de Instalação e Configuração

### Requisitos do Sistema
- PHP 8.2
- MySQL
- Composer
- Extensão `sockets` habilitada no PHP
- Apache com suporte a WebSockets

### Passos para Instalação
1. Clonar o repositório:
   ```bash
   git clone https://github.com/seu-repositorio/forca-backend.git
   cd forca-backend
   ```
2. Instalar dependências:
   ```bash
   composer install
   ```
3. Configurar o arquivo `.env`:
   ```bash
   cp .env.example .env
   ```
   - Editar com suas credenciais do banco de dados.

4. Executar as migrações do banco:
   ```bash
   php artisan migrate
   ```

5. Iniciar o servidor WebSocket:
   ```bash
   php bin/server.php
   ```

## 3. Arquitetura do Sistema  
- **`assets/`**: Contém arquivos estáticos, como imagens, estilos e scripts.  
- **`controllers/`**: Contém os handlers de conexões WebSocket, responsáveis por gerenciar eventos e interações.  
- **`models/`**: Contém os modelos de dados, como jogadores, salas e partidas.  
- **`config/`**: Armazena configurações gerais do sistema.  
- **`logs/`**: Diretório onde são armazenados os arquivos de log do servidor.  
- **`test/`**: Arquivo de testes de funcionalidades.  
- **`tools/`**: Reúne ferramentas auxiliares utilizadas no desenvolvimento e manutenção do sistema.  
- **`core/`**: Contém o núcleo da aplicação, incluindo a inicialização e funções essenciais.  


## 4. Rotas da API

### Criar uma nova sala
**Endpoint:** `POST /rooms`

**Corpo:**
```json
{
  "name": "Sala 1",
  "maxPlayers": 5
}
```

**Resposta:**
```json
{
  "roomId": "abc123",
  "name": "Sala 1",
  "maxPlayers": 5
}
```

### Conectar-se a uma sala via WebSocket
**Endpoint WebSocket:** `ws://seu-servidor.com/ws`

**Mensagem de conexão:**
```json
{
  "action": "join",
  "roomId": "abc123",
  "playerName": "Jogador1"
}
```

**Resposta:**
```json
{
  "status": "success",
  "message": "Jogador1 entrou na sala."
}
```

## 5. Modelos de Dados
### Tabela `rooms`
| Campo     | Tipo      | Descrição |
|-----------|----------|-----------|
| id        | INT      | Identificador da sala |
| name      | VARCHAR  | Nome da sala |
| maxPlayers | INT     | Máximo de jogadores |

### Tabela `players`
| Campo     | Tipo      | Descrição |
|-----------|----------|-----------|
| id        | INT      | Identificador do jogador |
| name      | VARCHAR  | Nome do jogador |
| roomId    | INT      | Sala onde está conectado |

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
  ws.send(JSON.stringify({ action: "join", roomId: "abc123", playerName: "Jogador1" }));
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

