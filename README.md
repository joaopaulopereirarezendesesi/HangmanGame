### Rodando o Front-End em React!
Acesse a pasta do projeto front-end: Navegue até a pasta frontend no seu terminal:

```
cd frontend
```

Execute o comando abaixo para instalar todas as dependências necessárias para o projeto:

```
npm install
```

Após a instalação das dependências, execute o comando abaixo para rodar o front-end localmente:

```
npm run dev
```

Opa professor! dei uma estudada no MVC e implementeni no projeto, estruturei da seguinte forma para as requisições do front:

?url=/controlador/ação/parametro

Por enquanto tem duas "ações": 

index:

Pega todos os usuarios cadastrados no banco e envia em forma de json;

show:

Pega apenas o úsuario com o id passado como parametro.

<b>Prof Matheus:</b> Depois preciso saber o que instalar para rodar o back-end em meu computador. :D

Tá na mão: 

Para rodar o Bakend:

Baixe XAMPP;

De start no MySQL e no Apache;

Vá no menu Íniciar e pesquise:
'Editar variáveis de ambiente do sistema' e de enter;

Clique em 'Variáveis de Ambiente...';

No primeira caixa de texto clique em 'Path';

Clique em 'Novo';

E caso tenha instalado o XAMPP na pasta recomendada pro ele cole:
C:\xampp\php

Caso não tenha vá até a pasta onde você e clique em xampp -> php e copie o indereço e cole lá;

Clone o repositório e vá até onde ovcê clonou;

abra a pasta de Backend e execute o cmd e escrava isso:
php -S localhost:4000 index.php
Isso vai ativa um servido imbutido no php para rodar o php!

