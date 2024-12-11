import React, { useState, useEffect } from "react";
import AppContext from "../../context/AppContext";
import { useNavigate } from "react-router-dom"; // Para navegação
import axios from "axios";
import Cookies from "js-cookie";
import styles from "./Login.module.css";

function Login() {
  const [isActive, setIsActive] = useState(false);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem("token");

    if (token) {
      navigate("/rooms");
    }
  }, []);

  const toggleActiveClass = () => {
    setIsActive((prevState) => !prevState);
  };

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
        console.log("User ID do cookie:", userId);
        console.log("Nickname do cookie:", nickname);

        localStorage.setItem("token", userId);

        navigate("/game");
      }
    } catch (error) {
      console.error("Erro na requisição:", error);
    }
  };

  return (
    <section className={styles.login}>
      <div
        className={`${styles.container} ${isActive ? styles.active : ""}`}
        id="container"
      >
        <div className={`${styles.form_container} ${styles.sign_up}`}>
          <form action="#">
            <h1>Create Account</h1>
            <input type="text" name="name" id="name" placeholder="Nome" />
            <input type="email" name="email" id="email" placeholder="Email" />
            <input
              type="password"
              name="password"
              id="password"
              placeholder="Password"
            />
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
