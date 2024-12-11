import React, { useEffect, useState } from "react";
import Cookies from "js-cookie";
import Players from "../../components/players";
import "./Game.css";

function Game() {
  const [userId, setUserId] = useState(null);
  const [nickname, setNickname] = useState("");

  useEffect(() => {
    // Obtém os cookies e define os estados
    setUserId(Cookies.get("user_id"));
    setNickname(Cookies.get("nickname"));
  }, []);

  const jogadores = [
    { nome: "João Paulo", pontos: 100 },
    { nome: "Felipe José", pontos: 80 },
    { nome: "Kauan", pontos: 50 },
    { nome: "Matheus", pontos: 30 },
    { nome: "Thayna", pontos: 0 },
  ];

  return (
    <main>
      <p>{nickname}</p>
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
