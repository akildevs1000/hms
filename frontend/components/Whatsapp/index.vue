<template>
  <v-card elevation="2" class="pa-4">
    <v-card-text>
      <!-- Add Account Button -->
      <v-btn class="mb-4" small color="blue" @click="addAccount" dark>
        <v-icon small>mdi-plus</v-icon> Account
      </v-btn>

      <!-- Loop through accounts and display each one in a card -->
      <div style="display: flex; gap: 12px">
        <v-card
          outlined
          v-for="(account, index) in accounts"
          :key="index"
          style="display: flex"
          max-width="250"
        >
          <v-card-text>
            <div class="text-h6 text--primary text-center">
              Whatsapp {{ index + 1 }}
            </div>

            <div v-if="account.qrCodeDisplay == true">
              <v-img
                max-width="250"
                max-height="250"
                :src="account.qrCodePath"
              ></v-img>
            </div>
            <div v-else style="max-width: 250px; padding: 15px">
              <v-img :src="account.placeHolderImage"></v-img>
            </div>

            <div class="text-center">
              <v-btn
                v-if="account.statusMessage"
                block
                small
                color="#139c4a"
                class="white--text"
              >
                {{ account.statusMessage }}
              </v-btn>
              <v-btn
                v-else
                block
                small
                color="deep-purple accent-4 white--text"
                @click="connect(account.clientId, index)"
              >
                Connect
              </v-btn>
            </div>
          </v-card-text>
        </v-card>
      </div>

      <!-- <pre>{{ accounts }}</pre> -->
    </v-card-text>
  </v-card>
</template>

<script>
import QRCode from "qrcode";

export default {
  data() {
    return {
      clicked: false,
      accounts: [], // Array to store multiple account info
      intervalId: null, // Store the interval ID
    };
  },
  async mounted() {
    await this.getWhatsappAccount(this.$auth.user.company_id);

    // this.intervalId = setInterval(() => {
    //   this.insertAccountIntoDB();
    // }, 5000); // 5000ms = 5 seconds
  },

  // beforeDestroy() {
  //   // Clear the interval when the component is destroyed to prevent memory leaks
  //   if (this.intervalId) {
  //     clearInterval(this.intervalId);
  //   }
  // },

  methods: {

    async insertAccountIntoDB() {
      if (this.clicked) { // Ensure `clicked` is true before executing
        try {
          let payload = {
            company_id: this.$auth.user.company_id,
            accounts: this.accounts,
          };
          console.log("🚀 ~ Sending payload:", payload);
          await this.$axios.post(`whatsapp-client-json`, payload);
          this.clicked = false;
        } catch (error) {
          console.error("Error inserting account:", error);
        }
      }
    },
    // Add a new account
    async getWhatsappAccount(company_id) {
      let { data } = await this.$axios.get(
        `whatsapp-client-json/${company_id}`
      );

      console.log(
        "🚀 ~ getWhatsappAccount ~ data?.accounts?.length:",
        data?.accounts?.length
      );

      if (data?.accounts?.length) {
        this.accounts = data.accounts;
        console.log(
          "🚀 ~ indise if ~ data?.accounts?.length:",
          data?.accounts?.length
        );
      }
    },
    async addAccount() {
      let clientId = `client_id_${Date.now()}`;
      this.accounts.push({
        clientId: clientId,
        ws: null,
        statusMessage: null,
        placeHolderImage: `/whatsapp-sleep.png`,
        qrCodePath: null,
        qrCodeDisplay: false,
        disconnectButton: false,
        connectButton: false,
        statusColor: null,
      });

      // await this.connect(clientId, this.accounts.length);
    },

    // Connect to a specific account
    async connect(clientId, index) {
      this.clicked = true;
      await this.connectWebSocket(clientId, index);
    },

    // Method to manage multiple WebSocket connections
    async connectWebSocket(clientId, index) {
      // Initialize the account in the array if it doesn't exist
      if (!this.accounts[index]) {
        this.accounts[index] = {
          clientId,
          ws: null,
          statusMessage: null,
          qrCodePath: null,
          qrCodeDisplay: false,
          disconnectButton: false,
          connectButton: false,
          statusColor: null,
        };
      }

      const account = this.accounts[index];
      // const wsUrl = `ws://localhost:5175?clientId=${clientId}`;
      const wsUrl = `wss://wa.mytime2cloud.com/ws/?clientId=${clientId}`;

      account.ws = new WebSocket(wsUrl);

      account.ws.onopen = () => {
        account.statusMessage = `Connected to the WebSocket server with clientId: ${clientId}`;
      };

      account.ws.onmessage = async (event) => {
        account.qrCodePath = null;
        account.qrCodeDisplay = false;

        account.statusMessage = null;
        account.disconnectButton = false;
        account.connectButton = false;
        account.statusColor = "";

        const data = JSON.parse(event.data);
        console.log("🚀 ~ account.ws.onmessage= ~ data:", data);

        if (data.event === "status") {
          account.statusMessage = data.data;
        } else if (data.event === "ready") {
          account.statusMessage = data.data;
          account.placeHolderImage = `/whatsapp.jpeg`;
          account.disconnectButton = true;
          account.statusColor = "success";
          await this.insertAccountIntoDB();
        } else if (data.event === "qr") {
          const qrCodeData = data.data;
          try {
            // Generate a QR code as a data URL
            const qrCodePath = await QRCode.toDataURL(qrCodeData, {
              color: {
                dark: "#000000", // Black QR code
                light: "#ffffff", // White background
              },
            });

            // Update the path to display the QR code
            account.qrCodePath = qrCodePath;
            account.qrCodeDisplay = true;
          } catch (error) {
            account.statusMessage = `Error generating QR code: ${error.message}`;
          }
        } else if (data.event === "destroy") {
          account.placeHolderImage = `/whatsapp-sleep.png`;
          account.statusMessage = data.data;
          account.connectButton = true;
          account.statusColor = "error";
        }
      };
    },
  },
};
</script>
