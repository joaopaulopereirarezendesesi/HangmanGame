# Documentação do Projeto - Jogo da Forca

## Visão Geral

Este projeto é um site de jogo da forca desenvolvido em PHP. Ele foi projetado para ser executado no Windows, mas também pode rodar em distribuições Linux. O sistema utiliza WebSockets para interação em tempo real, aproveitando as bibliotecas fornecidas pelo Composer.

O jogo da forca permite que os jogadores se conectem em tempo real para tentar adivinhar palavras. Ele possui um sistema de pontuação, histórico de partidas e suporte a múltiplos jogadores em uma mesma sessão.

## Requisitos do Sistema

- **Servidor Web**: Apache ou Nginx
- **PHP**: Versão 7.4 ou superior
- **Composer**: Para gerenciamento de dependências
- **Banco de Dados**: MySQL ou SQLite (configurável via `.env`)
- **Extensões PHP**: `sockets`, `mbstring`, `pdo`, `pdo_mysql` (caso use MySQL)

## Instalação

1. Clone o repositório do projeto:

   ```sh
   git clone https://github.com/seu-repositorio/jogo-da-forca.git
   cd jogo-da-forca
   ```

2. Instale as dependências utilizando o Composer:

   ```sh
   composer install
   ```

3. Configure o ambiente:

   - Renomeie o arquivo `.env.example` para `.env`
   - Edite o `.env` e configure os parâmetros necessários, incluindo conexão com o banco de dados e porta do servidor WebSocket.

4. Execute as migrações do banco de dados:

   ```sh
   php core/migrate.php
   ```

## Estrutura do Projeto

O projeto segue um padrão de namespaces para organização do código:

```json
{
  "autoload": {
    "psr-4": {
      "models\\": "models/",
      "Websocket\\": "websocket/",
      "core\\": "core/",
      "tools\\": "tools/"
    }
  }
}
```

As principais pastas do projeto são:

- `models/` - Contém as classes do modelo de dados, como jogadores, partidas e palavras
- `websocket/` - Implementação dos WebSockets, incluindo gerenciadores de conexão
- `core/` - Componentes essenciais do sistema, como configuração e gerenciamento de banco de dados
- `tools/` - Utilitários e funções auxiliares, como logs e validações
- `public/` - Arquivos acessíveis via web, como HTML, CSS e JS

## Dependências

O projeto usa as seguintes bibliotecas:

```json
"require": {
  "cboden/ratchet": "^0.4.4",
  "react/socket": "^1.16",
  "react/event-loop": "^1.5"
}
```

## Rodando o Front-End

### Passos para rodar o Front-End:

1. Acesse a pasta do projeto front-end:
   ```sh
   cd frontend
   ```
2. Instale as dependências necessárias:
   ```sh
   npm install
   ```
3. Execute o front-end localmente:
   ```sh
   npm run dev
   ```

## Rodando o Back-End

### Passos para rodar o Back-End:

1. Instalar o XAMPP (caso não tenha):
   - Baixe e instale o XAMPP.
2. Iniciar o MySQL e Apache:
   - Abra o XAMPP e inicie os serviços do MySQL e Apache.
3. Configurar o PHP no sistema:
   - Adicione o caminho `C:\xampp\php` às variáveis de ambiente do sistema.
4. Clonar o repositório:
   ```sh
   git clone https://github.com/seu-repositorio/jogo-da-forca.git
   ```
5. Rodar o servidor PHP:
   ```sh
   php -S localhost:4000 index.php
   ```

## Testando o WebSocket

### Passos para testar o WebSocket:

1. Baixe e instale o Composer.
2. Navegue até a pasta onde o arquivo `WSserver.php` está localizado.
3. Execute o seguinte comando para iniciar o servidor WebSocket:
   ```sh
   php WSserver.php
   ```
4. Acesse o diretório `/test` e abra os arquivos de teste.
5. Teste a troca de mensagens entre participantes e a comunicação entre salas.

Se houver erros de dependências, execute:

```sh
rm -rf vendor composer.lock
composer install
```

## Regras de Pontuação

### Desafiador

- **150 pontos iniciais**
- Palavras com mais de 4 letras repetidas: **-30 pontos**
- Palavras com 4 ou mais letras: **-50 pontos**
- Palavras entre 5 a 7 letras: **+10 pontos**
- Palavras entre 8 a 11 letras: **+30 pontos**
- Palavras com mais de 12 letras: **+50 pontos**
- Palavras com acentuação: **+20 pontos**
- Palavras com espaços: **+10 pontos por espaço**
- A cada dica dada: **-10 pontos**
- A cada minuto de atraso: **-10 pontos**

### Adivinhador

- **110 pontos iniciais**
- Acerto de letra: **+10 pontos**
- Acerto de palavra (com espaços): **+30 pontos**
- Dica dada: **-10 pontos**
- Acerto sem dica: **+10 pontos**

## Abstração do WebSocket para o Front-End

O WebSocket será iniciado pelo back-end ao receber a primeira requisição e verificará se a porta 8000 já está em uso antes de tentar iniciar novamente. O objetivo é que o back-end gerencie todo o servidor WebSocket, enquanto o front-end apenas faz as requisições necessárias.

Além disso, será implementada uma lógica de **recarga de estado** para quando o servidor WebSocket cair. O sistema verificará a tabela `played` no banco de dados e, caso haja registros, iniciará a restauração dos dados. O WebSocket enviará um gatilho `{type: reconnect}` para que o front-end envie uma nova requisição automaticamente, sem interromper a experiência do usuário.

## Contribuição

Se deseja contribuir, faça um fork do repositório, crie uma nova branch e envie um pull request com suas melhorias.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

