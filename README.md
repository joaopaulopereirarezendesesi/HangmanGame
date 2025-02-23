# 📜 Documentação do Projeto - Jogo da Forca

## 🎯 Visão Geral

Este projeto é um jogo da forca online, desenvolvido em **PHP**, utilizando **WebSockets** para possibilitar interatividade em tempo real entre múltiplos jogadores. O jogo conta com um **sistema de pontuação**, **histórico de partidas** e **suporte para vários jogadores simultaneamente**. 

Compatível com **Windows** e distribuições **Linux**.

---

## 🖥️ Requisitos do Sistema

✅ **Servidor Web**: Apache  
✅ **PHP**: Versão 7.4 ou superior  
✅ **React.js**
✅ **[Composer](https://getcomposer.org/)**: Gerenciador de dependências PHP  
✅ **Banco de Dados**: MySQL  
✅ **Extensões PHP Necessárias**: `sockets`, `mbstring`, `pdo`, `pdo_mysql`  

---

## ⚙️ Instalação

1️⃣ **Clone o repositório do projeto:**
   ```sh
   git clone https://github.com/joaopaulopereirarezendesesi/HangmanGame
   cd jogo-da-forca
   ```
2️⃣ **Instale o Composer pelo site oficial:**  
   🔗 [Baixar Composer](https://getcomposer.org/)

3️⃣ **Acesse a pasta do backend e instale as dependências:**
   ```sh
   composer install
   ```

---

## 📂 Estrutura do Projeto

📁 **models/** – Classes do modelo de dados (jogadores, partidas e palavras)  
📁 **websocket/** – Implementação dos WebSockets e gerenciamento de conexões  
📁 **core/** – Componentes essenciais, como configuração e banco de dados  
📁 **tools/** – Funções auxiliares, como logs e validações  

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

1️⃣ **Acesse a pasta do frontend:**
   ```sh
   cd frontend
   ```
2️⃣ **Instale as dependências:**
   ```sh
   npm install
   ```
3️⃣ **Execute o frontend localmente:**
   ```sh
   npm run dev
   ```

---

## 🔧 Executando o Backend

1️⃣ **Clone o repositório:**
   ```sh
   git clone https://github.com/joaopaulopereirarezendesesi/HangmanGame
   ```
2️⃣ **Instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html).**
3️⃣ **Adicione o caminho `C:\xampp\php` às variáveis de ambiente do sistema.**
4️⃣ **Configure o Apache (HTTP/HTTPS) no Windows:**

   ### 🔹 4.1 Habilitando OpenSSL:

   1️⃣ **No XAMPP, clique no **config** do Apache e logo após clique em php.ini.**

   2️⃣ **APERTE CTRL+F e digite "opemssl"**

   3️⃣ **Descomente ";extension=openssl" Retirando o ; da frente**

   4️⃣ **Caso o extension=php_openssl.dll esteja comentado, descomenti também**
   
   ### 🔹 4.2 Gerando certificado SSL:
   
   📂 **Vá até a pasta do XAMPP:**
   ```sh
   C:\xampp
   ```
   📂 **Acesse a pasta `/apache`** e crie um arquivo **`v3.ext`** e garanta que não tenha `.txt` no final da seguinte forma:

   1. No Gerenciador de Arquivos, vá até a barra superior e clique em Exibir.
   2. No menu Exibir, marque a caixa de seleção Extensões de nomes de arquivo.

   Isso permitirá que você veja e edite a extensão completa do arquivo, garantindo que ele seja **`salvo corretamente como`** **`v3.ext`** e **`não como`** **`v3.ext.txt`**.
   
   ✏️ **Adicione o seguinte conteúdo ao arquivo:**
   ```ext
   authorityKeyIdentifier=keyid,issuer
   basicConstraints=CA:FALSE
   keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
   subjectAltName = @alt_names
   [alt_names]
   DNS.1 = localhost
   ```

   📝 **Edite o arquivo `makecert.bat` e adicione ao final da linha: `bin\openssl x509 -in server.csr -out server.crt -req -signkey server.key -days 365`**
   ```sh
   -sha256 -extfile v3.ext
   ```

   🖥️ **Gere o certificado executando:**
   ```sh
   cd \xampp\apache
   makecert
   ```

   🔑 **Durante a configuração, insira:**
   - **Senha:** Exemplo `123456`  
   - **Country Name:** `BR`  
   - **Organization Name:** Nome da sua empresa  
   - **Common Name:** `localhost`  

   ✅ **Pronto! Seu certificado foi gerado.**

   ### 🔹 4.3 Instalando o certificado:
   
   📂 **Acesse:**
   ```sh
   C:\xampp\apache\conf\ssl.crt
   ```
   🛠️ **Importe o certificado, vá em "Procurar" e marque "Autoridade de Certificação Raiz Confiável".**
   🔒 **Confirme a instalação.**

   ### 🔹 4.4 Mudando a pasta root do Apache:
   
   Para alterar a pasta root (diretório padrão dos arquivos servidos pelo Apache), siga os passos abaixo:
   
   1️⃣ **Abra o arquivo de configuração do Apache**:
   ```sh
   C:\xampp\apache\conf\httpd.conf
   ```
   
   2️⃣ **Localize a linha:**
   ```apache
   DocumentRoot "C:/xampp/htdocs"
   <Directory "C:/xampp/htdocs">
   ```
   
   3️⃣ **Altere o caminho para a pasta de backend do projeto `Hangmangame`**, por exemplo:
   ```apache
   DocumentRoot "C:/Users/user/Documents/HangmanGame/Backend"
   <Directory "C:/Users/user/Documents/HangmanGame/Backend">
   ```

   4️⃣ **Abra o arquivo de configuração de ssl do Apache**: 
   ```sh
   C:\xampp\apache\conf\extra\httpd-ssl.conf
   ```

   5️⃣ **Localize a linha:**
   ```apache
   DocumentRoot "C:\xampp\htdocs"
   ```

   6️⃣ **Altere o caminho para a pasta de backend do projeto `Hangmangame`**, por exemplo:
   ```apache
   DocumentRoot "C:/Users/user/Documents/HangmanGame/Backend"
   <Directory "C:/Users/user/Documents/HangmanGame/Backend">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
   </Directory>
   ```

   7️⃣ **Salve o arquivo e reinicie o Apache no XAMPP** para que as alterações tenham efeito.
   
   ✅ Agora, o Apache servirá os arquivos a partir da nova pasta definida.

5️⃣ **Crie um .env na pasta de Backend. Modelo a ser seguido:**

   ```.env
   DB_HOST=
   DB_NAME=
   DB_USER=
   DB_PASS=
   JWT_SECRET=
   ```

6️⃣ **Inicie os serviços do MySQL e Apache no XAMPP.**

7️⃣ **Abra o phpmyadmin ou o MySQL no terminal, crie um banco de dados e importe o banco de testes disponivel no /DB do projeto**

8️⃣ **Instalando dependências**

   1️⃣ **Instale [Composer](https://getcomposer.org/)**

   2️⃣**Abra o CMD ou PowerShell na pasta /Backend e execute o seguinte comando**
   ```sh
   composer install
   ```

✅**Backend em funcionamento**

---

## 🏆 Regras de Pontuação

### 🎭 **Desafiador**

⭐ **150 pontos iniciais**

❌ Penalizações:
- Palavras com mais de 4 letras repetidas: **-30 pontos**
- Palavras com 4 ou mais letras: **-50 pontos**

🎁 Bônus:
- Palavras entre 5 a 7 letras: **+10 pontos**
- Palavras entre 8 a 11 letras: **+30 pontos**
- Palavras com mais de 12 letras: **+50 pontos**
- Palavras com acentuação: **+20 pontos**
- Palavras com espaços: **+10 pontos por espaço**

📉 Penalidades adicionais:
- Cada dica usada: **-10 pontos**
- Cada minuto de atraso: **-10 pontos**

### 🔍 **Adivinhador**

⭐ **110 pontos iniciais**

✅ Acertos:
- Letra correta: **+10 pontos**
- Palavra correta (com espaços): **+30 pontos**
- Acerto sem dica: **+10 pontos**

❌ Penalidade:
- Cada dica utilizada: **-10 pontos**

---

## 📜 Licença

🔖 Este projeto está licenciado sob a **GNU License**.