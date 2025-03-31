import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faFile, faFileAlt } from "@fortawesome/free-solid-svg-icons";
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
  const [profileImage, setProfileImage] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem("nickname");
    if (token) {
      navigate("/rooms");
    }
  }, [navigate]);

  useEffect(() => {
    if (error.length > 0) {
      const timeout = setTimeout(() => setError([]), 5000);
      return () => clearTimeout(timeout);
    }
  }, [error]);

  const toggleActiveClass = () => {
    setIsActive((prevState) => !prevState);
  };

  const handleImageChange = (e) => {
    setProfileImage(e.target.files[0]);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    try {
      const response = await axios.post(
        "http://localhost:80/?url=User/login",
        new URLSearchParams({ email, password }),
        {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest",
          },
          withCredentials: true,
        }
      );

      console.log(response.data);

      if (response.data.message === "Login successful") {
        const nickname = Cookies.get("nickname");
        localStorage.setItem("userName", nickname);

        navigate("/rooms");
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

    const formData = new FormData();
    formData.append("email", email);
    formData.append("nickname", nickname);
    formData.append("password", password);
    formData.append("confirm_password", confirmPassword);
    if (profileImage) {
      formData.append("profileImage", profileImage);
    }

    try {
      const response = await axios.post(
        "http://localhost:80/?url=User/create",
        formData,
        {
          headers: { "Content-Type": "multipart/form-data" },
          withCredentials: true,
        }
      );

      console.log(response.data);

      if (response.data.message === "User created successfully") {
        const loginResponse = await axios.post(
          "http://localhost:80/?url=User/login",
          new URLSearchParams({ email, password }),
          {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            withCredentials: true,
          }
        );

        console.log(loginResponse);

        if (loginResponse.data.message === "Login successful") {
          const nickname = Cookies.get("nickname");
          console.log("Nickname do cookie:", nickname);

          localStorage.setItem("nickname", nickname);
          navigate("/rooms");
        }
      } else if (response.data.errors || response.data.error) {
        setError(response.data.errors || response.data.error);
      }
    } catch (error) {
      console.error("Erro na requisição:", error);
      setError(error.response?.data?.error || "Erro ao criar usuário!");
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
            <input
              type="file"
              id="profileImage"
              accept="image/*"
              onChange={handleImageChange}
              style={{ display: "none" }}
            />
            <label htmlFor="profileImage" className={styles.fileUploadButton}>
              {profileImage ? (
                <>
                  <div className={styles.iconfile}>
                  <FontAwesomeIcon icon={faFileAlt} />
                  </div>
                  <p>Arquivo carregado!</p>
                </>
              ) : (
                <>
                  <div className={styles.iconfile}>
                    <FontAwesomeIcon icon={faFile} />
                  </div>
                  <p>Adicione sua imagem de perfil</p>
                </>
              )}
            </label>

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
          <form onSubmit={handleSubmit}>
            <h1>Sign In</h1>
            <input
              type="email"
              name="email"
              placeholder="Email"
              onChange={(e) => setEmail(e.target.value)}
              required
            />
            <input
              type="password"
              name="password"
              placeholder="Password"
              onChange={(e) => setPassword(e.target.value)}
              required
            />
            <a href="#">Forget Your Password?</a>
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
            <button className={styles.btn}>Sign In</button>
          </form>
        </div>

        <div className={styles.toggle_container}>
          <div className={styles.toggle}>
            <div className={`${styles.toggle_panel} ${styles.toggle_left}`}>
              <h1>Welcome back!</h1>
              <p>Enter your personal details to use all of site features</p>
              <button className={styles.hidden} onClick={toggleActiveClass}>
                Sign In
              </button>
            </div>

            <div className={`${styles.toggle_panel} ${styles.toggle_right}`}>
              <h1>Hello, friend!</h1>
              <p>
                Register with your personal details to use all of site features
              </p>
              <button className={styles.hidden} onClick={toggleActiveClass}>
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
