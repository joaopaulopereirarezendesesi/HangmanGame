import { useEffect, useState } from "react";
import styles from "./Rooms.module.css";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import Cookies from "js-cookie";
import ModalCriarSala from "../../components/ModalCriarSala/ModalCriarSala";
import { FaArrowRight, FaUser } from "react-icons/fa";
import { GrStatusGoodSmall } from "react-icons/gr";
import { GiCrown } from "react-icons/gi";

function Rooms() {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [rooms, setRooms] = useState([]);
  const [friends, setFriends] = useState([]);
  const [playersCount, setPlayersCount] = useState({});
  const [loading, setLoading] = useState(false);
  const [allRaking, setallRanking] = useState(0);
  const nickname = Cookies.get("nickname");
  const photo = Cookies.get("photo");
  const navigate = useNavigate();

  useEffect(() => {
    fetchRooms();
    fetchFriends();
  }, []);

  const fetchRooms = async () => {
    try {
      setLoading(true);
      const response = await axios.get(
        "http://localhost:80/?url=Room/getRooms",
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      setRooms(response.data.rooms);

      await Promise.all(
        response.data.rooms.map(async (room) => {
          await fetchOrganizeRoom(room.ID_O, room.ID_R);
          await countPlayers(room.ID_R);
        })
      );
    } catch (error) {
      console.error("Erro na requisição:", error);
    } finally {
      setLoading(false);
    }
  };

  const fetchOrganizeRoom = async (id_o, roomID) => {
    try {
      const response = await axios.get(
        `http://localhost:80/?url=User/getRoomOrganizer&id_o=${id_o}`,
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      setRooms((prevRooms) =>
        prevRooms.map((room) =>
          room.ID_R === roomID
            ? { ...room, organizerName: response.data.NICKNAME }
            : room
        )
      );
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  const fetchFriends = async () => {
    try {
      setLoading(true);
      const response = await axios.get(
        "http://localhost:80/?url=Friends/getFriendsById",
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );
      console.log(response.data.friends);
      if (
        response.data.friends &&
        Array.isArray(response.data.friends.friends)
      ) {
        setallRanking(response.data.friends.total_players);
        setFriends([...response.data.friends.friends]);
      } else {
        console.warn("Formato inesperado da API:", response.data);
        setFriends([]);
      }
    } catch (error) {
      console.error(
        "Erro ao buscar amigos:",
        error.response ? error.response.data : error.message
      );
    } finally {
      setLoading(false);
    }
  };

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
        [roomID]: response.data.players || 0,
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
            <img src={photo} alt="Imagem de perfil do usuário" />
            <h2>{nickname || "Anonymous"}</h2>
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
              <div>Carregando...</div>
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
                    <GiCrown className={styles.iconLeader} />
                    <p className={styles.titleRoom}>
                      {room.organizerName || "Carregando..."}
                    </p>
                    <p>
                      <FaUser className={styles.capacityRoom} />{" "}
                      {playersCount[room.ID_R] ?? "Carregando..."}/
                      {room.PLAYER_CAPACITY}
                    </p>
                    <p>
                      <GrStatusGoodSmall
                        className={
                          room.PRIVATE === 1
                            ? styles.privateRoom
                            : styles.publicRoom
                        }
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
            {loading ? (
              <div>Carregando...</div>
            ) : (
              friends
                .sort((a, b) => {
                  if (a.status === "online" && b.status !== "online") return -1;
                  if (a.status === "away" && b.status !== "away") return -1;
                  if (a.status === "offline" && b.status !== "offline")
                    return 1;
                  return 0;
                })
                .map((friend) => (
                  <div className={styles.friend} key={friend.rank}>
                    <img src={friend.foto} alt={friend.name} />
                    <div className={styles.statusContainer}>
                      <p className={styles.userName}>{friend.name}</p>
                      <p>
                        Ranking: {friend.rank}/{allRaking}
                      </p>
                      <div className={styles.statusWrapper}>
                        <span
                          className={`${styles.statusDot} ${
                            friend.status === "online"
                              ? styles.online
                              : friend.status === "away"
                              ? styles.away
                              : styles.offline
                          }`}
                        ></span>
                        <p
                          className={`${
                            friend.status === "online"
                              ? styles.onlineText
                              : friend.status === "away"
                              ? styles.awayText
                              : friend.status === "offline"
                              ? styles.offlineText
                              : ""
                          }`}
                        >
                          {friend.status === "online"
                            ? "Online"
                            : friend.status === "away"
                            ? "Ausente"
                            : "Offline"}
                        </p>
                      </div>
                    </div>
                  </div>
                ))
            )}
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
