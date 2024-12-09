import React, { useState, useEffect } from "react";
import styles from "./Login.module.css";
import axios from "axios";

function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  useEffect(() => {
    console.log(email);
    console.log(password);
  }, [email, password]);

  const handleSubmit = async (event) => {
    event.preventDefault();
    const remember = event.target.remember.checked;
    try {
      const response = await axios.post(
        "http://localhost:4000/?url=User/login",
        new URLSearchParams({
          email: email, // Certifique-se de enviar os dados corretos
          password: password,
          remember: remember ? "true" : "false",
        }),
        {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
        }
      );

      console.log("Resposta do servidor:", response.data);
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  return (
    <form id="meuFormulario" onSubmit={handleSubmit}>
      <input
        type="text"
        name="email"
        placeholder="Usuário"
        onChange={(e) => setEmail(e.target.value)}
        required
      />
      <input
        type="password"
        name="password"
        placeholder="Senha"
        onChange={(e) => setPassword(e.target.value)}
        required
      />
      <label>
        <input type="checkbox" name="remember" /> Lembrar de mim
      </label>
      <button type="submit">Entrar</button>
    </form>
  );
}

export default Login;
