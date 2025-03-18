import { useEffect, useState } from "react";
import styles from "./Rooms.module.css";

import { useNavigate } from "react-router-dom";

import axios from "axios";
import Cookies from "js-cookie";

import img from "../../assets/persona.jpg";

import ModalCriarSala from "../../components/ModalCriarSala/ModalCriarSala";

import { FaArrowRight, FaUser } from "react-icons/fa";
import { GrStatusGoodSmall } from "react-icons/gr";
import { GiCrown } from "react-icons/gi";

function Rooms() {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [rooms, setRooms] = useState([]);
  const nickname = Cookies.get("nickname");
  const userId = parseInt(Cookies.get("user_id"));
  const navigate = useNavigate();

  useEffect(() => {
    fetchRooms();
    fetchFriends();
  }, []);

  const fetchRooms = async () => {
    try {
      const response = await axios.get(
        "http://localhost:80/?url=Room/getRooms",
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      setRooms(response.data.rooms);

      // Passa o ID_R e o ID_O para obter o nome do organizador de cada sala
      console.log("SALAS: ", response.data.rooms);
      response.data.rooms.forEach((room) => {
        fetchOrganizeRoom(room.ID_O, room.ID_R);
      });
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  const fetchOrganizeRoom = async (id_o, roomID) => {
    try {
      const response = await axios.post(
        "http://localhost:80/?url=User/getRoomOrganizer",
        { id_o },
        {
          headers: { "Content-Type": "application/json" },
          withCredentials: true,
        }
      );

      // Atualize o nome do organizador para a sala correspondente
      setRooms((prevRooms) =>
        prevRooms.map((room) =>
          room.ID_R === roomID
            ? { ...room, organizerName: response.data.rooms.NICKNAME } // A resposta tem o nome do organizador
            : room
        )
      );
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  const fetchFriends = async () => {
    try {
      console.log(typeof userId);
      const response = await axios.post(
        "http://localhost:80/?url=Friends/getFriendsById",
        { id: userId },
        {
          headers: { "Content-Type": "application/json" },
          withCredentials: true,
        }
      );

      console.log("MEUS AMIGUXOS: ", response.data);
    } catch (error) {
      console.error(
        "Erro ao buscar amigos: ",
        error.response ? error.response.data : error.message
      );
    }
  };

  const handleLogout = () => {
    // Remove os dados de login
    Cookies.remove("token");
    Cookies.remove("user_id");
    Cookies.remove("nickname");
    localStorage.removeItem("token");
    localStorage.removeItem("userName");

    // Redireciona para a página de login
    navigate("/");
  };

  return (
    <main className={styles.rooms}>
      <section className={styles.content_section}>
        <header className={styles.playerInfo}>
          <div>
            <img src={img} alt="Imagem de perfil do usuário" />
            <h2>{nickname ? nickname : "Anonymous"}</h2>
          </div>

          <button
            className={styles.btnCreateRoom}
            onClick={() => setIsModalOpen(true)}
          >
            Criar sala
          </button>

          <button className={styles.btnLogout} onClick={handleLogout}>
            Logout
          </button>
        </header>
        <section className={styles.content}>
          <div className={styles.roomsGames}>
            {rooms.map((room) => (
              <div key={room.ID_R} className={styles.games}>
                <div>
                  <img src={img} alt="Imagem de perfil do usuário" />
                  <p className={styles.titleRoom}>{room.ROOM_NAME}</p>
                </div>
                <div className={styles.text}>
                  <p>
                    <GiCrown className={styles.iconLeader} />
                    {room.organizerName}
                  </p>
                  <p>
                    <FaUser className={styles.capacityRoom} /> 6/
                    {room.PLAYER_CAPACITY}
                  </p>
                  <p>
                    <GrStatusGoodSmall
                      className={`${
                        room.PRIVATE === 1
                          ? styles.privateRoom
                          : styles.publicRoom
                      }`}
                    />
                    {room.PRIVATE === 1 ? "Privado" : "Aberto"}
                  </p>
                </div>

                <button className={styles.btnGames}>
                  <FaArrowRight className={styles.iconBtnGames} />
                </button>
              </div>
            ))}
          </div>
          <aside className={styles.friends}>
            <div className={styles.friend}></div>
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
