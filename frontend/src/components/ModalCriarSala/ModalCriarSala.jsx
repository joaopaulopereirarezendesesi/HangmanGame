import React, { useState, useEffect } from "react";
import styles from "./ModalCriarSala.module.css";

const ModalCriarSala = ({ setIsModalOpen }) => {
  return (
    <div className={styles.modal}>
      <div className={styles.modalContent}>
        <h2>Cria sala</h2>
        <label>
          Nome:
          <input type="text" required />
        </label>
        <label>
          Senha:
          <input type="password" name="" id="" />
        </label>
        <label>
          Capacidade:
          <input type="number" name="capacity" id="capacity" min="1" max="20" />
        </label>
        <label>
          Tempo:
          <select name="points" id="points">
            <option value="1000">1000ms</option>
            <option value="1500">1500ms</option>
            <option value="2000">2000ms</option>
            <option value="2500">2500ms</option>
          </select>
        </label>
        <button>Salvar</button>
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
