# 📜 Documentação do Projeto - Jogo da Forca

## 🎯 Visão Geral

Este projeto é um jogo da forca online, desenvolvido em **PHP**, utilizando **WebSockets** para interatividade em tempo real entre múltiplos jogadores. O jogo conta com um **sistema de pontuação**, **histórico de partidas** e **suporte para vários jogadores simultaneamente**. Compatível com **Windows** e distribuições **Linux**.

---

## 🖥️ Requisitos do Sistema

- **Servidor Web**: Apache  
- **PHP**: Versão 7.4 ou superior  
- **React.js**: Para o Front-End  
- **[Composer](https://getcomposer.org/)**: Gerenciador de dependências PHP  
- **Banco de Dados**: MySQL  
- **Extensões PHP Necessárias**: `sockets`, `mbstring`, `pdo`, `pdo_mysql`  

---

## 📂 Estrutura do Projeto

- 📁 **models/** – Classes do modelo de dados (jogadores, partidas e palavras)  
- 📁 **websocket/** – Implementação dos WebSockets e gerenciamento de conexões  
- 📁 **core/** – Componentes essenciais, como configuração e banco de dados  
- 📁 **tools/** – Funções auxiliares, como logs e validações  

---

## 📦 Dependências

O projeto utiliza as seguintes bibliotecas:

```json
"require": {
   "cboden/ratchet": "v0.4.4",
   "react/socket": "v1.16.0",
   "react/event-loop": "v1.5.0",
   "vlucas/phpdotenv": "v5.6.1",
   "firebase/php-jwt": "v6.11.0",
   "phpmailer/phpmailer": "v6.9.3"
}
```

---

## 🚀 Executando o Frontend

1. **Acesse a pasta do frontend:**
   ```sh
   cd frontend
   ```
2. **Instale as dependências:**
   ```sh
   npm install
   ```
3. **Execute o frontend localmente:**
   ```sh
   npm run dev
   ```

---

## 🔧 Executando o Backend

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
   ```sh
   DB_HOST= <host do BD>
   DB_NAME= <nome do BD>
   DB_USER= <user do BD>
   DB_PASS= <senha do BD>
   JWT_SECRET= <chave de assinatura do JWT>
   ```

8. **Configure o banco de dados:**
   - Inicie os serviços do MySQL e Apache no XAMPP.
   - Execute esse INSERT antes de todos:
   ```sql
   INSERT INTO `photos`(`MATTER`, `ADDRESS`) VALUES 
   ('antropologia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\antropologia.png'),
   ('biologia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\biologia.png'),
   ('cienciapolitica','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\cienciapolitica.png'),
   ('filosofia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\filosofia.png'),
   ('fisica','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\fisica.png'),
   ('geografia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\geografia.png'),
   ('historia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\historia.png'),
   ('matematica','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\matematica.png'),
   ('psicologia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\psicologia.png'),
   ('sociologia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\sociologia.png')
   ```
   - Crie um banco e importe o banco disponível na pasta `/DB/Backup`.
   - Pupule o BD com o arquivo de INSERTS `/DB/Pupular`

9. **Instale as dependências do backend:**
   - Vá até a pasta Backend/Server e execute:
   ```sh
   composer install
   ```

✅ **Backend pronto para uso!**

>EndPoints, padrões de requisição e funcionalidades documentados em /Backend/README_BE.md

---

## 🔧 Executando o WebSocket

   1. **Crie o arquivo .env na psata Backend/WS:**
   ```sh
   DB_HOST= <host do BD>
   DB_NAME= <nome do BD>
   DB_USER= <user do BD>
   DB_PASS= <senha do BD>
   ```

   2. **Instale as dependências do WebSocket:**
   - Vá até a pasta Backend/WS e execute:
   ```sh
   composer install
   ```

>EndPoints, padrões de requisição e funcionalidades documentados em /Backend/README_WS.md

---

## 🔧 Ferramenta de depuração

   1. **Em arquivos que tem namespace em cima verifique se está sendo chamda a rota:**
   ```php
   use tools\Utils;
   ```

   - caso não estiver presente adicione e chame a rota de depuração assim:

   ```php
   Utils::debug_log("Variavel: " . $variavel);
   ```

   2. **Em arquivos sem namespace verifique se tem um require_once:**
   ```php
    require_once __DIR__ . '/../tools/helpers.php';
   ```

   - caso não estiver presente adicione e chame a rota de depuração assim:

   ```php
   tools\Utils::debug_log("Variavel: " . $variavel);
   ```
   
   . : Operador de concatenação de string

   Essa função vai gerar um log no arquivo /debug.log para debug ou qual quer outra coisa

---

## 📜 Licença

🔖 Este projeto está licenciado sob a **GNU License**.

INSERT INTO `photos`(`MATTER`, `ADDRESS`) VALUES 
('antropologia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\antropologia.png'),
('biologia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\biologia.png'),
('cienciapolitica','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\cienciapolitica.png'),
('filosofia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\filosofia.png'),
('fisica','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\fisica.png'),
('geografia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\geografia.png'),
('historia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\historia.png'),
('matematica','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\matematica.png'),
('psicologia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\psicologia.png'),
('sociologia','<Camnho até a pasta do projeto>\HangmanGame\Backend\Server\assets\photos\sociologia.png')