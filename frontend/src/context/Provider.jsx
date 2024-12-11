import React, { useState, useEffect } from "react";
import AppContext from "./AppContext";

const Provider = ({ children }) => {
  const [darkMode, setDarkMode] = useState(() => {
    // Carrega o estado inicial do sessionStorage ou usa false
    return localStorage.getItem("darkMode") === "true";
  });

  useEffect(() => {
    // Atualiza o sessionStorage sempre que darkMode mudar
    localStorage.setItem("darkMode", darkMode);
  }, [darkMode]);

  const value = {
    darkMode,
    setDarkMode,
  };
  return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
};

export default Provider;
