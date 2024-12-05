<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../CSS/cr.css" />
    <link rel="stylesheet" href="../CSS/choise.css" />
    <title>HangmanGame</title>
  </head>
  <body>
    <form action="POST">
      <section class="formClass">
        <input type="radio" id="challenging" name="role" value="challenging" />
        <label for="challenging"
          ><i class="bx bx-edit"></i>
          <p>Desafiante</p></label
        >
        <input type="radio" id="guesser" name="role" value="guesser" />
        <label for="guesser"><i class="bx bx-search-alt"></i>
        <p>Adivinhador</p></label>
        <button type="submit" id="btnSub" class="btnSub">oi</button>
      </section>
    </form>
  </body>
</html>

