import { BrowserRouter as Router, Routes, Route } from "react-router-dom";

import PrivateRoute from "./protectedroute/PrivateRoute";

import Login from "./pages/Login/Login";
import Game from "./pages/Game/Game";
import Provider from "./context/Provider";
import Darkmode from "./components/DarkMode/Darkmode";
import Rooms from "./pages/Rooms/Rooms";

function App() {
  return (
    <Provider>
      <Router>
        <Routes>
          <Route path="/" element={<Login />} />

          <Route
            path="/game"
            element={
              <PrivateRoute>
                <Game />
              </PrivateRoute>
            }
          />
          <Route
            path="/rooms"
            element={
              <PrivateRoute>
                <Rooms />
              </PrivateRoute>
            }
          />

          <Route path="/darkmode" element={<Darkmode />} />
        </Routes>
      </Router>
    </Provider>
  );
}

export default App;
