import React, { useContext } from "react";
import styles from "./Players.module.css";
import AppContext from "../../context/AppContext";

import img from "../../assets/persona.jpg";

function Players({ nome, pontos }) {
  const { darkMode } = useContext(AppContext);
  return (
    <div
      className={
        darkMode ? `${styles.player} ${styles.dark}` : `${styles.player}`
      }
    >
      <img src={img} alt="Foto do player" />
      <div className={styles.contentplayer}>
        <h1>{nome}</h1>
        <p>{pontos} pontos</p>
      </div>
    </div>
  );
}

export default Players;
