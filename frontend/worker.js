const WebSocket = require("ws");
require("dotenv").config();

const fs = require("fs");
let socket = new WebSocket(process.env.SOCKET_ENDPOINT);
socket.onopen = () => console.log("connected\n");
socket.onerror = () => console.log("error\n");

socket.onmessage = ({ data }) => {
    let {
        UserCode: UserID,
        DeviceID,
        RecordDate: LogTime,
        RecordNumber: SerialNumber,
    } = JSON.parse(data).Data;

    if (UserID !== 0) {
        let str = `${UserID},${DeviceID},${LogTime},${SerialNumber}`;
        fs.appendFileSync("/var/www/ideahrms/backend/logs/logs.csv", str + "\n");
        fs.appendFileSync("/var/www/staging/ideahrms/backend/logs/logs.csv", str + "\n");
    }
};
