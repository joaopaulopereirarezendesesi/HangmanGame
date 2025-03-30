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
  const [playersCount, setPlayersCount] = useState({});
  const [loading, setLoading] = useState(false); // Estado de carregamento
  const nickname = Cookies.get("nickname");
  const navigate = useNavigate();

  useEffect(() => {
    fetchRooms();
    fetchFriends();
  }, []);

  // Função para buscar as salas
  const fetchRooms = async () => {
    try {
      setLoading(true); // Inicia o carregamento
      const response = await axios.get(
        "http://localhost:80/?url=Room/getRooms",
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      setRooms(response.data.rooms);

      // Usando Promise.all para garantir que todas as requisições sejam feitas corretamente
      await Promise.all(
        response.data.rooms.map(async (room) => {
          await fetchOrganizeRoom(room.ID_O, room.ID_R);
          await countPlayers(room.ID_R);
        })
      );
      setLoading(false); // Finaliza o carregamento
    } catch (error) {
      setLoading(false); // Finaliza o carregamento em caso de erro
      console.error("Erro na requisição:", error);
    }
  };

  // Função para buscar o organizador da sala
  const fetchOrganizeRoom = async (id_o, roomID) => {
    try {
      const response = await axios.get(
        `http://localhost:80/?url=User/getRoomOrganizer`,
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      setRooms((prevRooms) =>
        prevRooms.map((room) =>
          room.ID_R === roomID
            ? { ...room, organizerName: response.data.rooms.NICKNAME }
            : room
        )
      );
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  // Função para buscar os amigos
  const fetchFriends = async () => {
    try {
      const response = await axios.get(
        "http://localhost:80/?url=Friends/getFriendsById",
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      console.log("MEUS AMIGUXOS:", response.data);
    } catch (error) {
      console.error(
        "Erro ao buscar amigos:",
        error.response ? error.response.data : error.message
      );
    }
  };

  // Função para contar os jogadores em uma sala
  const countPlayers = async (roomID) => {
    try {
      const response = await axios.post(
        "http://localhost:80/?url=Room/countPlayers",
        new URLSearchParams({ roomId: roomID }),
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      setPlayersCount((prevCounts) => ({
        ...prevCounts,
        [roomID]: response.data.players,
      }));
    } catch (error) {
      console.error(
        "Erro ao buscar jogadores:",
        error.response ? error.response.data : error.message
      );
    }
  };

  const handleLogout = () => {
    Cookies.remove("jwt");
    Cookies.remove("nickname");
    localStorage.removeItem("jwt");
    localStorage.removeItem("nickname");
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
            {loading ? (
              <div>Carregando...</div> // Feedback visual de carregamento
            ) : (
              rooms.map((room) => (
                <div key={room.ID_R} className={styles.games}>
                  <div>
                    <img
                      src={room.MODALITY_IMG}
                      alt="Imagem de perfil do usuário"
                    />
                    <p className={styles.titleRoom}>{room.ROOM_NAME}</p>
                  </div>
                  <div className={styles.text}>
                    <p>
                      <GiCrown className={styles.iconLeader} />
                      {room.organizerName}
                    </p>
                    <p>
                      <FaUser className={styles.capacityRoom} />{" "}
                      {playersCount[room.ID_R] !== undefined
                        ? playersCount[room.ID_R]
                        : "Carregando..."}
                      /{room.PLAYER_CAPACITY}
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
                    <p>🎮 {room.MODALITY}</p>
                  </div>

                  <button className={styles.btnGames}>
                    <FaArrowRight className={styles.iconBtnGames} />
                  </button>
                </div>
              ))
            )}
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
      {isModalOpen && (
        <ModalCriarSala
          setIsModalOpen={setIsModalOpen}
          fetchRooms={fetchRooms}
        />
      )}
    </main>
  );
}

export default Rooms;
