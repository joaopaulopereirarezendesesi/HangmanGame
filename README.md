# Documentação do Projeto - Jogo da Forca

## Visão Geral

Este projeto é um site de jogo da forca desenvolvido em PHP, utilizando WebSockets para permitir a interação em tempo real entre múltiplos jogadores. Ele foi projetado para ser compatível tanto com Windows quanto com distribuições Linux. O jogo conta com um sistema de pontuação, histórico de partidas e suporte a múltiplos jogadores simultaneamente.

## Requisitos do Sistema

- **Servidor Web**: Apache ou Nginx  
- **PHP**: Versão 7.4 ou superior  
- **Composer**: Para gerenciamento de dependências  
- **Banco de Dados**: MySQL ou SQLite  
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
3. Configure o ambiente no arquivo de configuração PHP.
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

### Principais Diretórios:

- **models/** – Classes do modelo de dados (jogadores, partidas e palavras)
- **websocket/** – Implementação dos WebSockets e gerenciamento de conexões
- **core/** – Componentes essenciais, como configuração e banco de dados
- **tools/** – Funções auxiliares, como logs e validações
- **public/** – Arquivos acessíveis via web (HTML, CSS e JS)

## Dependências

O projeto utiliza as seguintes bibliotecas:

```json
"require": {
  "cboden/ratchet": "^0.4.4",
  "react/socket": "^1.16",
  "react/event-loop": "^1.5"
}
```

## Executando o Front-End

1. Acesse a pasta do front-end:
   ```sh
   cd frontend
   ```
2. Instale as dependências:
   ```sh
   npm install
   ```
3. Execute o front-end localmente:
   ```sh
   npm run dev
   ```

## Executando o Back-End

1. Instale o XAMPP (caso necessário).
2. Inicie os serviços do MySQL e Apache no XAMPP.
3. Adicione o caminho `C:\xampp\php` às variáveis de ambiente do sistema.
4. Clone o repositório:
   ```sh
   git clone https://github.com/seu-repositorio/jogo-da-forca.git
   ```
5. Inicie o servidor PHP:
   ```sh
   php -S localhost:4000 index.php
   ```

## Testando o WebSocket

1. Navegue até a pasta onde o arquivo `WSserver.php` está localizado.
2. Execute o seguinte comando para iniciar o servidor WebSocket:
   ```sh
   php WSserver.php
   ```
3. Acesse o diretório `/test` e abra os arquivos de teste.
4. Teste a troca de mensagens entre participantes e a comunicação entre salas.
5. Se houver erros de dependências, execute:
   ```sh
   rm -rf vendor composer.lock
   composer install
   ```

## Regras de Pontuação

### **Desafiador**

- 150 pontos iniciais  
- Palavras com mais de 4 letras repetidas: **-30 pontos**  
- Palavras com 4 ou mais letras: **-50 pontos**  
- Palavras entre 5 a 7 letras: **+10 pontos**  
- Palavras entre 8 a 11 letras: **+30 pontos**  
- Palavras com mais de 12 letras: **+50 pontos**  
- Palavras com acentuação: **+20 pontos**  
- Palavras com espaços: **+10 pontos por espaço**  
- A cada dica dada: **-10 pontos**  
- A cada minuto de atraso: **-10 pontos**  

### **Adivinhador**

- 110 pontos iniciais  
- Acerto de letra: **+10 pontos**  
- Acerto de palavra (com espaços): **+30 pontos**  
- Dica dada: **-10 pontos**  
- Acerto sem dica: **+10 pontos**  

## Abstração do WebSocket para o Front-End

O WebSocket será iniciado pelo back-end ao receber a primeira requisição e verificará se a porta 8000 já está em uso antes de tentar iniciar novamente. O objetivo é que o back-end gerencie todo o servidor WebSocket, enquanto o front-end apenas faz as requisições necessárias.

Além disso, será implementada uma lógica de recarga de estado para quando o servidor WebSocket cair. O sistema verificará a tabela `played` no banco de dados e, caso haja registros, iniciará a restauração dos dados. O WebSocket enviará um gatilho `{type: reconnect}` para que o front-end envie uma nova requisição automaticamente, sem interromper a experiência do usuário.

## Contribuição

Se deseja contribuir, siga os passos abaixo:

1. Faça um **fork** do repositório.
2. Crie uma **nova branch** com um nome descritivo:
   ```sh
   git checkout -b feature-nova-funcionalidade
   ```
3. Faça suas alterações e adicione os arquivos modificados:
   ```sh
   git add .
   ```
4. Realize um commit seguindo um padrão de mensagens claras:
   ```sh
   git commit -m "feat: Adicionada nova funcionalidade X"
   ```
5. Envie suas alterações para o seu repositório forkado:
   ```sh
   git push origin feature-nova-funcionalidade
   ```
6. Abra um **Pull Request** no repositório original e aguarde a revisão.

## Licença

Este projeto está licenciado sob a **GNU License**.

