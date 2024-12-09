import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom"; // Para navegação
import axios from "axios";
import Cookies from "js-cookie";
import "./Login.css";

function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const navigate = useNavigate(); // Hook para navegação

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
          email: email,
          password: password,
          remember: remember ? "true" : "false",
        }),
        {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          withCredentials: true,
        }
      );

      console.log(response.data.cookies);

      if (response.data.message === "Login bem-sucedido") {
        const userId = Cookies.get("user_id");
        const nickname = Cookies.get("nickname");
        console.log("User ID do cookie:", response.data.cookies.user_id);
        console.log("Nickname do cookie:", response.data.cookies.nickname);

        console.log(userId);
        console.log(nickname);

        navigate("/game");
      }
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  return (
    // <form id="meuFormulario" onSubmit={handleSubmit}>
    //   <input
    //     type="text"
    //     name="email"
    //     placeholder="Usuário"
    //     onChange={(e) => setEmail(e.target.value)}
    //     required
    //   />
    //   <input
    //     type="password"
    //     name="password"
    //     placeholder="Senha"
    //     onChange={(e) => setPassword(e.target.value)}
    //     required
    //   />
    //   <label>
    //     <input type="checkbox" name="remember" /> Lembrar de mim
    //   </label>
    //   <button type="submit">Entrar</button>
    // </form>

    <section className="sec">
      <div className="div">p</div>
    </section>
  );
}

export default Login;
