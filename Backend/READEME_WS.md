# WebSocket PHP - Guia de Uso

## Introdução
Este projeto implementa um servidor WebSocket em PHP utilizando a biblioteca [Ratchet](http://socketo.me/). Ele permite a comunicação em tempo real entre clientes, incluindo funcionalidades como chat, salas, pedidos de amizade e reconexão.

## Como Executar o Servidor WebSocket

Para iniciar o servidor WebSocket, execute:
```sh
php WSserver.php
```

Por padrão, ele será executado na porta 8000. Se quiser rodar em uma porta diferente, edite o script de inicialização.

## Eventos Suportados

### 1. `chat`
**Envio de mensagem no chat**
```json
{
  "type": "chat",
  "room": 1,
  "user": "user1",
  "message": "Fala Cambada"
}
```
#### Resposta do servidor:
```json
{
  "type": "chat",
  "room": 1,
  "user": "user1",
  "message": "Fala Cambada"
}
```

### 2. `joinRoom`
**Entrar em uma sala específica**
```json
{
  "type": "joinRoom",
  "room": 1
}
```
#### Resposta do servidor:
```json
{
  "success": "Joined room 1"
}
```

### 3. `friendRequest`
**Solicitação de amizade entre usuários, tem 2 tipos:**
```json
{
  "type": "friendRequest",
  "fromUser": 1,
  "toUser": 2,
  "actionRequest": "friendrequest"
}
```
e
```json
{
  "type": "friendRequest",
  "fromUser": 1,
  "toUser": 2,
  "actionRequest": "responserequest",
  "response": "<accepted e rejected>"
}
```
#### Resposta do servidor(friendrequest):
- Remetente:
```json
{
  "type": "friendRequest",
  "fromUser": 1,
  "toUser": 2,
  "status": <"already exists ou sent">
}
```
- Destinatário: 
```json
{
  "type": "friendRequest",
  "fromUser": 1,
  "toUser": 2,
  "status": "received"
}
```
#### Resposta do servidor(responserequest):
Não tem resposta ao destinatário do pedido
```json
{
  "type": "friendRequest",
  "fromUser": 1,
  "toUser": 2,
  "response": "<accepted e rejected>"
}
```

### 3. `login`
**Realizar login automaticamente quando a conexão for estabelecida**
```json
{
  "type": "login",
  "password": "123456",  
  "id_bd": 1
}
```
Ao conectar-se ao servidor WebSocket, o cliente automaticamente enviará a requisição de login com o `id_bd` do usuário. O servidor processará essa requisição e marcará o usuário como online no banco de dados, além disso, isso serve para a lógica interna do WebSocket, para que o servidor consiga identificar que é esse usuário para a aplicação.
#### Resposta do servidor:
```json
{
  "type": "login",
  "success": true
}
```

## Tratamento de Erros

Se houver erros na mensagem enviada, o servidor responderá com um JSON contendo o tipo `error` e uma mensagem descritiva:
```json
{
  "type": "error",
  "message": "Invalid message"
}
```
