import styles from "./Players.module.css";

function Players({ nome, pontos }) {
  return (
    <div className={styles.player}>
      <span></span>
      <div className={styles.contentplayer}>
        <h1>{nome}</h1>
        <p>{pontos} pontos</p>
      </div>
    </div>
  );
}

export default Players;
