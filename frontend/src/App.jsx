import "./App.css";
import Players from "./components/players";

function App() {
  const jogadores = [
    { nome: "João Paulo", pontos: 100 },
    { nome: "Felipe José", pontos: 80 },
    { nome: "Kauan", pontos: 50 },
    { nome: "Matheus", pontos: 30 },
    { nome: "Thayna", pontos: 0 },
  ];

  return (
    <main>
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

export default App;
