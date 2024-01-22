const { formToJSON } = require("axios");
const net = require("net");
const { Console } = require("console");
// node server1.js > output.log &
require("dotenv").config({ path: "../backend/.env" });
const port = 4801;
const fs = require("fs");
// make a new logger
const myLogger = new Console({
  stdout: fs.WriteStream("output.txt"),
  stderr: fs.WriteStream("errStdErr.txt"),
});

let { Client } = require("pg");

const DB = new Client({
  host: process.env.DB_HOST,
  user: process.env.DB_USERNAME,
  port: process.env.DB_PORT,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_DATABASE,
});

const server = net.createServer((socket) => {
  logMessage("Client connected");

  socket.on("data", (data, rinfo) => {
    logMessage(
      "\n----------------------------------------------------------------------------------------------------------"
    );
    let TodayDatetime = getTime();
    //logMessage(`\nDevice Data Received:${data}`);
    const strData = data.toString();

    const dataBuffer = Buffer.from(data);
    // Convert the Buffer to a hexadecimal string
    let hexString = dataBuffer.toString("hex");
    // console.log(`Received data in hexadecimal---------:  ${hexString}`);

    hexString = hexString.replace(/(.{2})/g, "$1 ");

    logMessage(`Device Data Received:  ${hexString}`);
  });

  socket.on("end", () => {
    logMessage("Client disconnected");
  });

  socket.on("error", (error) => {
    logMessage(`Socket Error: ${error.message}`, 1);
  });
});

server.on("error", (error) => {
  logMessage(`Server Error: ${error.message}`, 1);
});

server.listen(port, () => {
  logMessage(`TCP socket server is running on port: ${port} `);

  ///////////////readDeviceData();
});
function logMessage(message, error = 0) {
  if (message == "") return false;
  const timestamp = getTime();
  const date = getDate();
  console.log(`${timestamp}: ${message} `);
  if (error)
    fs.appendFileSync(
      date + "-device-error-message.txt",
      `\n${message} - ${timestamp}`
    );
  else
    fs.appendFileSync(
      date + "-device-message.txt",
      `\n${timestamp} : ${message}   `
    );
}
async function readDeviceData() {
  let powerstatus = 0;
  let serial_number = "<Buffer 4c 75 25 02 39 e4>";
  let data =
    "<Buffer fc fc fd ba 00 23 80 01 00 16 05 00 00 00 00 00 4c 75 25 02 39 e4 14 3e 01 01 00 01 00 05 81 00 c6 00 00 00 00 00 00 04 42 fc 0d 0a>";

  let bufferarray = data.split(" ");
  if (bufferarray[29]) {
    if (bufferarray[29] == "01") {
      powerstatus = 1;
    }
  }

  await DB.connect();

  try {
    const insertQuery = `
       INSERT INTO device_logs (serial_number, status, raw_data)
       VALUES ($1, $2, $3)
     `;
    const values = [serial_number, powerstatus, data];

    await DB.query(insertQuery, values);
    logMessage("Data inserted successfully");

    // Call the function to update the device table
    await updateDeviceTable(DB, serial_number, powerstatus);
  } catch (error) {
    logMessage("Error inserting data:" + error, 1);
  } finally {
    DB.end();
    logMessage("Database connection closed");
  }
}
async function updateDeviceTable(DB, serial_number, powerstatus) {
  try {
    const updateQuery = `
     UPDATE devices
     SET latest_status = $2
     WHERE serial_number = $1
   `;
    const values = [serial_number, powerstatus];

    await DB.query(updateQuery, values);
    logMessage("Data updated successfully");
  } catch (error) {
    logMessage("Error updating data:" + error, 1);
  }
}
function getTime() {
  let date_ob = new Date();

  // current date
  // adjust 0 before single digit date
  let date = ("0" + date_ob.getDate()).slice(-2);

  // current month
  let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);

  // current year
  let year = date_ob.getFullYear();

  // current hours
  let hours = date_ob.getHours();

  // current minutes
  let minutes = date_ob.getMinutes();

  // current seconds
  let seconds = date_ob.getSeconds();

  // prints date in YYYY-MM-DD format
  //console.log(year + "-" + month + "-" + date);

  // prints date & time in YYYY-MM-DD HH:MM:SS format
  return (
    year +
    "-" +
    month +
    "-" +
    date +
    " " +
    hours +
    ":" +
    minutes +
    ":" +
    seconds
  );
}

function getDate() {
  let date_ob = new Date();

  // current date
  // adjust 0 before single digit date
  let date = ("0" + date_ob.getDate()).slice(-2);

  // current month
  let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);

  // current year
  let year = date_ob.getFullYear();

  // current hours
  let hours = date_ob.getHours();

  // current minutes
  let minutes = date_ob.getMinutes();

  // current seconds
  let seconds = date_ob.getSeconds();

  // prints date in YYYY-MM-DD format
  //console.log(year + "-" + month + "-" + date);

  // prints date & time in YYYY-MM-DD HH:MM:SS format
  return year + "-" + month + "-" + date;
}
