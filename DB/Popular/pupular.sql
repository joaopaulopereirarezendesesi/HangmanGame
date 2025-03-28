INSERT INTO users (ID_U, NICKNAME, EMAIL, PASSWORD, ONLINE, PHOTO)
VALUES
    (UUID(), 'Jogador1', 'jogador1@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'offline', 'http://localhost:80/assets/photos/jogador1.png'),
    (UUID(), 'Jogador2', 'jogador2@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'online', 'http://localhost:80/assets/photos/jogador2.png'),
    (UUID(), 'Jogador3', 'jogador3@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'offline', 'http://localhost:80/assets/photos/jogador3.png'),
    (UUID(), 'Jogador4', 'jogador4@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'away', 'http://localhost:80/assets/photos/jogador4.png'),
    (UUID(), 'Jogador5', 'jogador5@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'offline', 'http://localhost:80/assets/photos/jogador5.png'),
    (UUID(), 'Jogador6', 'jogador6@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'online', 'http://localhost:80/assets/photos/jogador6.png'),
    (UUID(), 'Jogador7', 'jogador7@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'offline', 'http://localhost:80/assets/photos/jogador7.png'),
    (UUID(), 'Jogador8', 'jogador8@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'away', 'http://localhost:80/assets/photos/jogador8.png'),
    (UUID(), 'Jogador9', 'jogador9@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'online', 'http://localhost:80/assets/photos/jogador9.png'),
    (UUID(), 'Jogador10', 'jogador10@email.com', '$argon2id$v=19$m=65536,t=4,p=1$NnBYc1ZnQmYuQlF5V2hPcg$UeodXoEgkE8/EtiQ0c+HBDeHLq4b73k8po7zp94gWl4', 'offline', 'http://localhost:80/assets/photos/jogador10.png');

INSERT INTO ranking (ID_U, POSITION, AMOUNT_OF_WINS, NUMBER_OF_GAMES, POINT_AMOUNT)
SELECT ID_U, 1, 10, 20, 1500 FROM users WHERE NICKNAME = 'Jogador1'
UNION ALL
SELECT ID_U, 2, 8, 18, 1200 FROM users WHERE NICKNAME = 'Jogador2'
UNION ALL
SELECT ID_U, 3, 6, 16, 1000 FROM users WHERE NICKNAME = 'Jogador3'
UNION ALL
SELECT ID_U, 4, 5, 14, 800 FROM users WHERE NICKNAME = 'Jogador4'
UNION ALL
SELECT ID_U, 5, 4, 13, 750 FROM users WHERE NICKNAME = 'Jogador5'
UNION ALL
SELECT ID_U, 6, 3, 12, 700 FROM users WHERE NICKNAME = 'Jogador6'
UNION ALL
SELECT ID_U, 7, 2, 11, 650 FROM users WHERE NICKNAME = 'Jogador7'
UNION ALL
SELECT ID_U, 8, 2, 10, 600 FROM users WHERE NICKNAME = 'Jogador8'
UNION ALL
SELECT ID_U, 9, 1, 9, 500 FROM users WHERE NICKNAME = 'Jogador9'
UNION ALL
SELECT ID_U, 10, 0, 8, 400 FROM users WHERE NICKNAME = 'Jogador10';

INSERT INTO friends (ID_U, ID_A)
SELECT u1.ID_U, u2.ID_U
FROM users u1
JOIN users u2 ON u1.ID_U != u2.ID_U;