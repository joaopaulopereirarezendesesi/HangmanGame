import React, { useState, useEffect } from "react";
import AppContext from "../../context/AppContext";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import Cookies from "js-cookie";
import styles from "./Login.module.css";

function Login() {
  const [isActive, setIsActive] = useState(false);
  const [error, setError] = useState([]);
  const [email, setEmail] = useState("");
  const [nickname, setNickname] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (token) {
      navigate("/rooms");
    }
  }, [navigate]);

  useEffect(() => {
    if (error) {
      const timeout = setTimeout(() => {
        setError("");
      }, 5000);

      return () => clearTimeout(timeout);
    }
  }, [error]);

  const toggleActiveClass = () => {
    setIsActive((prevState) => !prevState);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    const remember = event.target.remember.checked;

    console.log(email);
    console.log(password);

    try {
      const response = await axios.post(
        "http://localhost:80/?url=User/login",
        new URLSearchParams({
          email,
          password,
          remember: remember ? "true" : "false",
        }),
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      if (response.data.message === "Login bem-sucedido") {
        const userId = Cookies.get("user_id");
        const nickname = Cookies.get("nickname");
        console.log("User ID do cookie:", userId);
        console.log("Nickname do cookie:", nickname);

        localStorage.setItem("token", userId);
        localStorage.setItem("userName", nickname);
        navigate("/game");
      }
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  const handleRegister = async (event) => {
    event.preventDefault();

    if (password !== confirmPassword) {
      setError("As senhas não coincidem!");
      return;
    }

    try {
      const response = await axios.post(
        "http://localhost:4000/?url=User/create",
        new URLSearchParams({
          email,
          nickname,
          password,
        }),
        {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          withCredentials: true,
        }
      );

      console.log(response.data);

      if (response.data.success) {
        alert("Usuário cadastrado com sucesso!");

        console.log(email, password);
        // Realizando o login após o cadastro
        const loginResponse = await axios.post(
          "http://localhost:4000/?url=User/login",
          new URLSearchParams({
            email,
            password,
            remember: "true",
          }),
          {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            withCredentials: true,
          }
        );

        console.log(loginResponse);

        if (loginResponse.data.message === "Login bem-sucedido") {
          const userId = Cookies.get("user_id");
          const nickname = Cookies.get("nickname");
          console.log("User ID do cookie:", userId);
          console.log("Nickname do cookie:", nickname);

          localStorage.setItem("token", userId); // Armazenando token no localStorage
          navigate("/rooms"); // Redireciona após login
        }
      } else if (response.data.errors || response.data.error) {
        console.log(response.data.errors);
        setError(response.data.errors || response.data.error);
      }
    } catch (error) {
      console.error("Erro na requisição:", error);
      setError("Ocorreu um erro ao cadastrar. Tente novamente.");
    }
  };

  return (
    <section className={styles.login}>
      <div
        className={`${styles.container} ${isActive ? styles.active : ""}`}
        id="container"
      >
        <div className={`${styles.form_container} ${styles.sign_up}`}>
          <form onSubmit={handleRegister}>
            <h1>Create Account</h1>
            <input
              type="text"
              name="nickname"
              placeholder="Nome"
              value={nickname}
              onChange={(e) => setNickname(e.target.value)}
              required
            />
            <input
              type="email"
              name="email"
              placeholder="Email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
            <input
              type="password"
              name="password"
              placeholder="Password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
            <input
              type="password"
              name="confirmPassword"
              placeholder="Confirm Password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              required
            />
            {error && (
              <div className={styles.error}>
                {Array.isArray(error) ? (
                  Object.entries(error).map(([key, message]) => (
                    <p key={key}>{message}</p>
                  ))
                ) : (
                  <p>{error}</p>
                )}
              </div>
            )}
            <button className={styles.btn}>Sign Up</button>
          </form>
        </div>

        <div className={`${styles.form_container} ${styles.sign_in}`}>
          <form action="meuFormulario" onSubmit={handleSubmit}>
            <h1>Sign In</h1>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="Email"
              onChange={(e) => setEmail(e.target.value)}
              required
            />
            <input
              type="password"
              name="password"
              id="password"
              placeholder="Password"
              onChange={(e) => setPassword(e.target.value)}
              required
            />

            <label className={styles.custom_checkbox}>
              <input
                type="checkbox"
                name="remember"
                className={styles.remember}
              />
              <span className={styles.checkmark}></span>
              Lembrar-me
            </label>
            <a href="#">Forget Your Password?</a>
            <button className={styles.btn}>Sign In</button>
          </form>
        </div>

        <div className={styles.toggle_container}>
          <div className={styles.toggle}>
            <div className={`${styles.toggle_panel} ${styles.toggle_left}`}>
              <h1>Welcome back!</h1>
              <p>Enter your personal details to use all of site features</p>
              <button
                className={styles.hidden}
                id="login"
                onClick={toggleActiveClass}
              >
                Sign In
              </button>
            </div>

            <div className={`${styles.toggle_panel} ${styles.toggle_right}`}>
              <h1>Hello, friend!</h1>
              <p>
                Register with your personal details to use all of site features
              </p>
              <button
                className={styles.hidden}
                id="register"
                onClick={toggleActiveClass}
              >
                Sign Up
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

export default Login;
