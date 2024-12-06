import styles from "./Players.module.css";

import img from "../assets/persona.jpg";

function Players({ nome, pontos }) {
  return (
    <div className={styles.player}>
      <img src={img} alt="Foto do player" />
      <div className={styles.contentplayer}>
        <h1>{nome}</h1>
        <p>{pontos} pontos</p>
      </div>
    </div>
  );
}

export default Players;
