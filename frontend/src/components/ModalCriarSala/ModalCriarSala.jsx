import React, { useState, useEffect } from "react";
import Cookies from "js-cookie";
import styles from "./ModalCriarSala.module.css";
import axios from "axios";
import RoomStatus from "../RoomStatus/RoomStatus";

const ModalCriarSala = ({ setIsModalOpen }) => {
  const [roomName, setRoomName] = useState(""); // Nome da sala
  const [privateRoom, setPrivateRoom] = useState(false); // Sala privada
  const [password, setPassword] = useState(""); // Senha
  const [capacity, setCapacity] = useState(2); // Capacidade
  const [time, setTime] = useState("1000"); // Tempo
  const [points, setPoints] = useState("1000"); // Pontos

  const handleSave = async () => {
    try {
      const userId = Cookies.get("user_id");
      const token = Cookies.get("token");

      console.log(token);
      console.log(userId);
      console.log(roomName);
      console.log(privateRoom);
      console.log(password);
      console.log(capacity);
      console.log(time);
      console.log(points);

      console.log(privateRoom ? password : null);

      const response = await axios.post(
        "http://localhost:80/?url=Room/createRoom",
        new URLSearchParams({
          id: userId,
          room_name: roomName,
          private: privateRoom,
          password: privateRoom ? password : null,
          player_capacity: capacity,
          time_limit: time,
          points: points,
        }),
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      console.log(response.data);

      console.log("SALA CRIADA!");
      setRoomName("");
      setPrivateRoom(false);
      setPassword("");
      setCapacity(2);
      setPoints("1000");
      setTime("1000");
      setIsModalOpen(false);
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  return (
    <div className={styles.modal}>
      <div className={styles.modalContent}>
        <h2>Cria sala</h2>

        <label>
          Nome:
          <input
            type="text"
            value={roomName}
            onChange={(e) => setRoomName(e.target.value)}
            required
          />
        </label>

        <label>
          Status:
          <RoomStatus
            setPrivateRoom={setPrivateRoom}
            privateRoom={privateRoom}
          />
        </label>

        {privateRoom === true && (
          <label>
            Senha:
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
          </label>
        )}

        <label>
          Capacidade:
          <input
            type="number"
            value={capacity}
            onChange={(e) => setCapacity(e.target.value)}
            min="2"
            max="20"
          />
        </label>

        <label htmlFor="time">
          Tempo:
          <select
            name="time"
            id="time"
            value={time}
            onChange={(e) => setTime(e.target.value)}
          >
            <option value="1000">1000ms</option>
            <option value="1500">1500ms</option>
            <option value="2000">2000ms</option>
            <option value="2500">2500ms</option>
          </select>
        </label>

        <label htmlFor="points">
          Pontos:
          <select
            name="points"
            id="points"
            value={points}
            onChange={(e) => setPoints(e.target.value)}
          >
            <option value="1000">1000</option>
            <option value="1500">1500</option>
            <option value="2000">2000</option>
            <option value="2500">2500</option>
          </select>
        </label>

        <button onClick={handleSave}>Salvar</button>
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
