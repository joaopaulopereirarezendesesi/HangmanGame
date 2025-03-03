# API PHP - Guia de Uso

## Introdução
Esta API fornece funcionalidades para gerenciamento de usuários e salas, incluindo criação e participação em salas, autenticação de usuários e recuperação de senha.

## Endpoints

### 1. Criar Usuário
**Endpoint:** `/user/create`
**Método:** `POST`

**Parâmetros:**
```json
{
  "nickname": "usuario123",
  "email": "usuario@email.com",
  "password": "Senha@123",
  "confirm_password": "Senha@123"
}
```

**Resposta de Sucesso:**
```json
{
  "message": "Usuário criado com sucesso!"
}
```

**Erros Possíveis:**
- `400`: Senha inválida ou não coincide
- `400`: E-mail inválido

---
### 2. Login
**Endpoint:** `/user/login`
**Método:** `POST`

**Parâmetros:**
```json
{
  "email": "usuario@email.com",
  "password": "Senha@123",
  "remember": true
}
```

**Resposta de Sucesso:**
```json
{
  "message": "Login bem-sucedido",
  "user_id": 1
}
```

**Erros Possíveis:**
- `401`: Credenciais inválidas

---
### 3. Criar Sala
**Endpoint:** `/room/create`
**Método:** `POST`

**Parâmetros:**
```json
{
  "id": 1,
  "room_name": "MinhaSala",
  "private": true,
  "password": "123456",
  "player_capacity": 5,
  "time_limit": 10,
  "points": 2000
}
```

**Resposta de Sucesso:**
```json
{
  "idsala": 10,
  "id_o": 1,
  "nomesala": "MinhaSala",
  "privacao": true,
  "capacidade": 5,
  "tampodasala": 10,
  "pontos": 2000
}
```

**Erros Possíveis:**
- `400`: Nome de sala já em uso
- `400`: Senha obrigatória para salas privadas
- `400`: Capacidade ou tempo limite inválidos

---
### 4. Entrar em Sala
**Endpoint:** `/room/join`
**Método:** `POST`

**Parâmetros:**
```json
{
  "roomId": 10,
  "userId": 1,
  "password": "123456"
}
```

**Resposta de Sucesso:**
```json
{
  "message": "Entrou na sala com sucesso."
}
```

**Erros Possíveis:**
- `400`: Sala não encontrada
- `400`: Senha inválida
- `400`: Sala cheia

---
### 5. Remover Jogador da Sala
**Endpoint:** `/room/removePlayer`
**Método:** `POST`

**Parâmetros:**
```json
{
  "roomId": 10,
  "userId": 1
}
```

**Resposta de Sucesso:**
```json
{
  "message": "Jogador removido com sucesso."
}
```

**Erros Possíveis:**
- `400`: Sala não encontrada

---
### 6. Recuperar Senha
**Endpoint:** `/user/recoverPassword`
**Método:** `POST`

**Parâmetros:**
```json
{
  "id": 1,
  "oldPassword": "SenhaAntiga",
  "newPassword": "NovaSenha@123",
  "c_newPassword": "NovaSenha@123"
}
```

**Erros Possíveis:**
- `400`: Senha antiga incorreta
- `400`: Senhas novas não coincidem

---
### 7. Logout
**Endpoint:** `/user/logout`
**Método:** `POST`

**Resposta de Sucesso:**
```json
{
  "message": "Logout bem-sucedido"
}
```

## Tratamento de Erros
Se houver erros na requisição, a API responderá com um JSON contendo o tipo `error` e uma mensagem descritiva:
```json
{
  "type": "error",
  "message": "Mensagem de erro."
}
```

