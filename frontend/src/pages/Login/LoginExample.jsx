import React, { useState } from "react";
import styles from "./LoginExample.module.css";

function LoginExample() {
  // Estado para controlar se o container está ativo ou não
  const [isActive, setIsActive] = useState(false);

  // Função para alternar entre as classes
  const toggleActiveClass = () => {
    setIsActive((prevState) => !prevState);
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
          <form action="#">
            <h1>Sign In</h1>
            <input type="email" name="email" id="email" placeholder="Email" />
            <input
              type="password"
              name="password"
              id="password"
              placeholder="Password"
            />

            <label className={styles.custom_checkbox}>
              <input type="checkbox" className={styles.remember} />
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

export default LoginExample;
