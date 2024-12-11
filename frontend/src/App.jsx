import "./App.css";

import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Login from "./pages/Login/Login";
import Game from "./pages/Game/Game";
import LoginExample from "./pages/Login/LoginExample";

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/game" element={<Game />} />
        <Route path="/testing" element={<LoginExample />} />
      </Routes>
    </Router>
  );
}

export default App;
