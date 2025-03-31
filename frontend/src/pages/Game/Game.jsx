import { useEffect, useState, useContext } from "react";
import Cookies from "js-cookie";
import Players from "../../components/Players/Players";
import AppContext from "../../context/AppContext";
import "./Game.css";
import Darkmode from "../../components/DarkMode/Darkmode";

function Game() {
  const [userId, setUserId] = useState(null);
  const [nickname, setNickname] = useState("");
  const { darkMode } = useContext(AppContext);

  useEffect(() => {
    setUserId(Cookies.get("user_id"));
    setNickname(Cookies.get("nickname"));
    console.log(darkMode);
  }, []);

  const jogadores = [
    { nome: "João Paulo", pontos: 100 },
    { nome: "Felipe José", pontos: 80 },
    { nome: "Kauan", pontos: 50 },
    { nome: "Matheus", pontos: 30 },
    { nome: "Thayna", pontos: 0 },
  ];

  return (
    <main className={darkMode ? "dark" : ""}>
      <Darkmode />
      <section className="game">
        <div className="players">
          {jogadores.map((jogador, index) => (
            <Players key={index} nome={jogador.nome} pontos={jogador.pontos} />
          ))}
        </div>
        <div className="word"></div>
        <div className="chat"></div>
      </section>
    </main>
  );
}

export default Game;
