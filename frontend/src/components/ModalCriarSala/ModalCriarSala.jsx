import React, { useState, useEffect } from "react";
import styles from "./ModalCriarSala.module.css";

const ModalCriarSala = ({ setIsModalOpen }) => {
  return (
    <div className={styles.modal}>
      <div className={styles.modalContent}>
        <h2>Editar Administrador</h2>
        <label>
          Username:
          {/* <input type="text" value={username} required /> */}
        </label>
        <label>
          Email:
          {/* <input type="email" value={email} required /> */}
        </label>
        <label>
          Role:
          {/* <select value={role} required></select> */}
        </label>

        <button onClick={() => setIsModalOpen(false)}>Close</button>
      </div>
    </div>
  );
};

// Nome da sala
// Private/Public
// Password
// Capacidade players
// Tempo Chat
// Pontos para vitoria

export default ModalCriarSala;
