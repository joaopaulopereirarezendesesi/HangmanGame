import React, { useContext, useEffect } from "react";
import AppContext from "../../context/AppContext";
import styles from "./Darkmode.module.css";
import { IoSunny } from "react-icons/io5";
import { FaMoon } from "react-icons/fa";

function Darkmode() {
  const { darkMode, setDarkMode } = useContext(AppContext);

  useEffect(() => {
    console.log(darkMode);
  }, [darkMode]);

  const handleToggle = () => {
    setDarkMode((prevMode) => !prevMode); // Alterna entre true e false
  };

  return (
    <section className={styles.darkmode}>
      <input
        className={styles.toggle}
        type="checkbox"
        id="toggle"
        checked={darkMode} // Sincroniza o estado com o contexto
        onChange={handleToggle} // Atualiza o estado ao clicar
      />

      <div className={styles.display}>
        <label htmlFor="toggle">
          <div className={styles.circle}>
            <IoSunny className={styles.sun} />
            <FaMoon className={styles.moon} />
          </div>
        </label>
      </div>
    </section>
  );
}

export default Darkmode;
