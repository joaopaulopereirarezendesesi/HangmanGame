import React, { useContext, useEffect } from "react";
import styles from "./RoomStatus.module.css";

import { FaUsers } from "react-icons/fa";
import { GiPadlock } from "react-icons/gi";

function RoomStatus({ privateRoom, setPrivateRoom }) {
  return (
    <section className={styles.statusroom}>
      <input
        type="checkbox"
        id="statusroom_toggle"
        className={styles.statusroom_toggle}
      />
      <label
        htmlFor="statusroom_toggle"
        onClick={() => setPrivateRoom(!privateRoom)}
        className={styles.statusroom_label}
      >
        <FaUsers className={styles.iconPublicRoom} />
        <GiPadlock className={styles.iconPrivateRoom} />
      </label>
    </section>
  );
}

export default RoomStatus;
