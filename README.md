## Rodando o Front-End

### Passos para rodar o Front-End:

1. **Acesse a pasta do projeto front-end**:
   - Navegue até a pasta `frontend` no seu terminal:
     ```bash
     cd frontend
     ```

2. **Instale as dependências necessárias**:
   - Execute o comando abaixo para instalar todas as dependências do projeto:
     ```bash
     npm install
     ```

3. **Execute o front-end localmente**:
   - Após a instalação das dependências, execute o comando abaixo para rodar o front-end:
     ```bash
     npm run dev
     ```

## Rodando o Back-End

### Passos para rodar o Back-End:

1. **Instalar o XAMPP**:
   - Se não tiver o XAMPP, baixe e instale [XAMPP](https://www.apachefriends.org/pt_br/index.html).

2. **Iniciar o MySQL e Apache**:
   - Abra o XAMPP e inicie os serviços do MySQL e Apache.

3. **Configurar o PHP**:
   - Vá para "Editar variáveis de ambiente do sistema" e adicione o caminho do PHP ao PATH. Se o XAMPP foi instalado na pasta padrão, adicione:
     ```plaintext
     C:\xampp\php
     ```

4. **Clonar o repositório**:
   - Clone o repositório e acesse a pasta do back-end.

5. **Rodar o servidor PHP**:
   - Execute o seguinte comando na pasta do back-end:
     ```bash
     php -S localhost:4000 index.php
     ```

## Testando o WebSocket

### Passos para testar o WebSocket:

1. **Baixe o Composer**:
   - Acesse o site oficial do [Composer](https://getcomposer.org/) e baixe o instalador para o seu sistema operacional.
   - Após o download, siga as instruções de instalação para garantir que o Composer esteja corretamente configurado no seu ambiente.

2. **Abrir o terminal no diretório `/Backend`**:
   - Navegue até a pasta onde o arquivo `WSserver.php` está localizado.
   - Execute o seguinte comando para iniciar o servidor WebSocket:
     ```bash
     php WSserver.php
     ```

3. **Acessar o diretório `/test`**:
   - Vá até a pasta `/test` dentro do seu projeto.
   - Abra os arquivos de teste, por exemplo, `testeWS1.html`, `testeWS2.html`, etc.

4. **Testando as mensagens entre diferentes salas**:
   - Ao abrir dois ou mais arquivos de teste em diferentes abas do navegador, simule a interação entre diferentes salas.
   - Teste se as mensagens enviadas em uma sala chegam corretamente apenas aos participantes daquela sala e se não há vazamento de mensagens entre salas.

5. **Verificação**:
   - Envie uma mensagem em uma das salas e observe se a outra sala não recebe a mensagem.
   - Teste a troca de mensagens entre participantes da mesma sala e se a funcionalidade de comunicação entre os usuários está funcionando como esperado.

> **Observação**: Caso não funcione e comece a dar muitos erros, pode ser porque as dependências não estão devidamente instaladas no seu PC. Nesse caso, apague as pastas `/vendor` e o arquivo `/composer.lock`, deixando apenas o `composer.json`. Em seguida, abra o terminal na pasta do back-end e execute o seguinte comando para reinstalar as dependências:
   ```bash
   composer install
   ```
---

## Regras de Pontuação

### Desafiador - Regras de Pontuação

- **150 pontos iniciais**:
  - **Palavras com mais de 4 letras repetidas**: -30 pontos
  - **Palavras com 4 ou mais letras**: -50 pontos
  - **Palavras entre 5 a 7 letras**: +10 pontos
  - **Palavras entre 8 a 11 letras**: +30 pontos
  - **Palavras com mais de 12 letras**: +50 pontos
  - **Palavras com acentuação**: +20 pontos
  - **Palavras com espaços**: +10 pontos por espaço
  - **A cada dica dada**: -10 pontos
  - **A cada minuto de atraso para o acerto**: -10 pontos

### Adivinhador - Regras de Pontuação

- **110 pontos iniciais**:
  - **Acerto de letra**: +10 pontos
  - **Acerto de palavra (com espaços)**: +30 pontos
  - **Dica dada**: -10 pontos
  - **Acerto sem dica**: +10 pontos

> Opa, professor, não sei se é isso, mas se for o que estou pensando, os dois Composer são coisas diferentes: o composer.json e o composer.lock. O composer.json contém as    dependências e as configurações do projeto, enquanto o composer.lock garante que as versões exatas das dependências sejam instaladas em diferentes máquinas. Quando você executa o comando composer install, o Composer usa o composer.lock para garantir que as versões das dependências sejam consistentes. Qual quer problema siga os passos que coloquei em "**Testando o WebSocket**"
>
> Eu quero abstrair o WebSocket para o front-end. Já comecei a fazer isso iniciando o servidor WebSocket quando a primeira requisição for feita e depois ele vai verificar se a porta 8000 (a porta do WebSocket) está em uso. Se estiver, ele não tentará iniciar mais. A ideia é que quem vai tratar do servidor WebSocket será o back-end, então o front-end só vai precisar requisitar o servidor, e o resto o back-end cuida.