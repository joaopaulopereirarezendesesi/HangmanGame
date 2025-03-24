import React, { useState } from "react";
import Cookies from "js-cookie";
import styles from "./ModalCriarSala.module.css";
import axios from "axios";
import RoomStatus from "../RoomStatus/RoomStatus";

const ModalCriarSala = ({ setIsModalOpen }) => {
  const [roomName, setRoomName] = useState("");
  const [privateRoom, setPrivateRoom] = useState(false);
  const [password, setPassword] = useState("");
  const [capacity, setCapacity] = useState(2);
  const [time, setTime] = useState("1000");
  const [points, setPoints] = useState("1000");
  const [modality, setModality] = useState("Livre");

  const handleSave = async () => {
    try {
      const userId = Cookies.get("user_id");

      console.log("Enviando:", {
        id: userId,
        room_name: roomName,
        private: privateRoom ? "1" : "0",
        password: privateRoom ? password : "",
        player_capacity: capacity,
        time_limit: time,
        points: points,
        modality: modality
      });

      const formData = new URLSearchParams();
      formData.append("id", userId);
      formData.append("room_name", roomName);
      formData.append("private", privateRoom ? "1" : "0");
      formData.append("password", privateRoom ? password : "");
      formData.append("player_capacity", String(capacity));
      formData.append("time_limit", String(time));
      formData.append("points", String(points));
      formData.append("modality", modality);

      const response = await axios.post(
        "http://localhost:80/?url=Room/createRoom",
        formData,
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
      setModality("Livre");
      setIsModalOpen(false);
    } catch (error) {
      console.error("Erro na requisição:", error);
      console.log(error.response?.data);
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
            onChange={(e) => setCapacity(Number(e.target.value))}
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
            onChange={(e) => setTime(Number(e.target.value))}
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
            onChange={(e) => setPoints(Number(e.target.value))}
          >
            <option value="1000">1000</option>
            <option value="1500">1500</option>
            <option value="2000">2000</option>
            <option value="2500">2500</option>
          </select>
        </label>

        <label htmlFor="modality">
          Modalidade:
          <select
            name="modality"
            id="modality"
            value={modality}
            onChange={(e) => setModality(e.target.value)}
          >
            <option value="Livre">Livre</option>
            <option value="Antropologia">Antropologia</option>
            <option value="Biologia">Biologia</option>
            <option value="CienciaPolitica">Ciência Política</option>
            <option value="Filosofia">Filosofia</option>
            <option value="Fisica">Física</option>
            <option value="Historia">História</option>
            <option value="Matematica">Matemática</option>
            <option value="Psicologia">Psicologia</option>
            <option value="Sociologia">Sociologia</option>
          </select>
        </label>

        <button onClick={handleSave}>Salvar</button>
        <button onClick={() => setIsModalOpen(false)}>Close</button>
      </div>
    </div>
  );
};

export default ModalCriarSala;
