### Rodando o Front-End em React

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

3. **Rodando o front-end localmente**:
   - Após a instalação das dependências, execute o comando abaixo para rodar o front-end:
     ```bash
     npm run dev
     ```

---

### Forma da requisição:

Como fazer a requisição:

```
?url=/controlador/ação/parametro
```

---

### Como Rodar o Back-End

Para rodar o Backend:

1. **Baixe o XAMPP**:
   - Se você não tiver o XAMPP instalado, baixe e instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html).

2. **Inicie o MySQL e o Apache**:
   - Abra o XAMPP e inicie os serviços do MySQL e do Apache.

3. **Configuração do PHP**:
   - Vá até o menu **Iniciar** e pesquise por:
     ```
     Editar variáveis de ambiente do sistema
     ```
   - Clique em **Variáveis de Ambiente**.

4. **Configuração do PATH**:
   - Na primeira caixa de texto, clique em **Path** e depois em **Novo**.
   - Caso tenha instalado o XAMPP na pasta recomendada, cole:
     ```
     C:\xampp\php
     ```
   - Se não, vá até a pasta onde o XAMPP foi instalado, localize a pasta `php` e copie o caminho até ela.

5. **Clone o repositório**:
   - Clone o repositório em sua máquina local e acesse a pasta do Backend.

6. **Rodando o servidor PHP**:
   - Abra a pasta de Backend no terminal e execute o seguinte comando para rodar o servidor PHP integrado:
     ```bash
     php -S localhost:4000 index.php
     ```
     Isso vai ativar um servidor embutido no PHP para rodar o back-end localmente!

7. **Baxando dependencias**:
   - Vá até o site do Composer;
   - Download;
   - Baixe o executavel e o execute;
   - Não se preucupe com o resto! as depencias já estão baixadas no projeto.

---

### Quer testar o WebSocket?

1. **Abra o terminal no diretório da pasta /Backend**
   - Execute o comando "php WSserver";

2. **Vá até /test e abra os 4 arquivos de "testeWS"**
   - 2 abriram na sala 1 e 2 na sala 2

3. **Teste, manade mensagem e veja se:**
   - As mensagens chegam até o outro cara da mesma sala 
   - As mensagens não chegam na outro sala
   - ... Teste oque quiser!


### Desafiador - Regras de Pontuação

**150 pontos iniciais:**

- **Palavras com mais de 4 letras repetidas:** -30 pontos
- **Palavras com quantidade de letras igual ou maior que 4:** -50 pontos
- **Palavras com quantidade de letras entre 5 a 7:** +10 pontos
- **Palavras com quantidade de letras entre 8 a 11:** +30 pontos
- **Palavras com mais de 12 letras:** +50 pontos
- **Palavras com acentuação:** +20 pontos
- **Palavras com espaços:** a cada espaço +10 pontos
- **A cada dica dada:** -10 pontos
- **A cada minuto que o acerto levar:** -10 pontos

### Adivinhador - Regras de Pontuação

**110 pontos iniciais:**

- **A cada acerto de letra:** +10 pontos
- **A cada acerto de palavra (caso tenha espaços):** +30 pontos
- **A cada dica dada:** -10 pontos
- **Acerto sem dica:** +10 pontos

---
