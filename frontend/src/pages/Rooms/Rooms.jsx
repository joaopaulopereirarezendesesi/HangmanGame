import React, { useEffect, useState } from "react";
import styles from "./Rooms.module.css";

import img from "../../assets/persona.jpg";

import ModalCriarSala from "../../components/ModalCriarSala/ModalCriarSala";

import { FaArrowRight, FaUser } from "react-icons/fa";
import { GrStatusGoodSmall } from "react-icons/gr";
import { GiCrown } from "react-icons/gi";

function Rooms() {
  const [isModalOpen, setIsModalOpen] = useState(false);

  return (
    <main className={styles.rooms}>
      <section className={styles.content_section}>
        <header className={styles.playerInfo}>
          <div>
            <img src={img} alt="Imagem de perfil do usuário" />
            <h2>Nome jogador</h2>
          </div>

          <button
            className={styles.btnCreateRoom}
            onClick={() => setIsModalOpen(true)}
          >
            Criar sala
          </button>
        </header>
        <section className={styles.content}>
          <div className={styles.roomsGames}>
            <div className={styles.games}>
              <div>
                <img src={img} alt="Imagem de perfil do usuário" />
                <p className={styles.titleRoom}>Sala do JP</p>
              </div>
              <div className={styles.text}>
                <p>
                  <GiCrown className={styles.iconLeader} /> João Paulo
                </p>
                <p>
                  <FaUser className={styles.capacityRoom} /> 6/10
                </p>
                <p>
                  <GrStatusGoodSmall className={styles.statusRoom} /> Privado
                </p>
              </div>

              <button className={styles.btnGames}>
                <FaArrowRight className={styles.iconBtnGames} />
              </button>
            </div>
          </div>
          <aside className={styles.friends}>
            <div className={styles.friend}></div>
            <div className={styles.friend}></div>
            <div className={styles.friend}></div>
            <div className={styles.friend}></div>
            <div className={styles.friend}></div>
          </aside>
        </section>
      </section>

      {isModalOpen && <ModalCriarSala setIsModalOpen={setIsModalOpen} />}
    </main>
  );
}

export default Rooms;
