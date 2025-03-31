import { useState, useEffect } from "react";
import axios from "axios";
import styles from "./TwoFASetup.module.css";

function TwoFASetup({ onClose }) {
  const [qrCode, setQrCode] = useState("");
  const [secret, setSecret] = useState("");

  useEffect(() => {
    const fetch2FAData = async () => {
      try {
        const response = await axios.get(
          "http://localhost:80/?url=User/generateSecretImage",
          {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            withCredentials: true,
          }
        );

        console.log(response.data);

        if (response.data.image) {
          setQrCode(`data:image/png;base64,${response.data.image}`);
        } else {
          console.error("Imagem não encontrada na resposta da API");
        }

        if (response.data.secret) {
          setSecret(response.data.secret);
        } else {
          console.error("Código secreto não encontrado na resposta da API");
        }
      } catch (error) {
        console.error("Erro ao buscar dados do 2FA:", error);
      }
    };
    fetch2FAData();
  }, []);

  return (
    <div className={styles.modalOverlay}>
      <div className={styles.modalContainer}>
        <h1>Configuração de Autenticação de Dois Fatores</h1>
        <div className={styles.qrCodeContainer}>
          {qrCode && (
            <img
              src={qrCode}
              alt="QR Code para 2FA"
              className={styles.qrCode}
            />
          )}
        </div>
        {secret && (
          <div className={styles.secretContainer}>
            <p>
              Seu código secreto: <strong>{secret}</strong>
            </p>
          </div>
        )}
        <div className={styles.buttonContainer}>
          <button className={styles.btn} onClick={onClose}>
            Concluir
          </button>
        </div>
      </div>
    </div>
  );
}

export default TwoFASetup;
