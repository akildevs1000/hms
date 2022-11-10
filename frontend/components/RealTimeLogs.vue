<template>
  <v-data-table :headers="headers" :items="logs" :items-per-page="5" dense>
  </v-data-table>
</template>

<script>
export default {
  layout: "login",
  auth: false,
  data: () => ({
    // logo : '/LTLOGO.png',
    // logo: "https://smarthr.dreamguystech.com/orange/assets/img/logo2.png",

    headers: [
      // {
      //   text: "Image",
      //   align: "center",
      //   sortable: false,
      //   value: "RecordImage"
      // },
      {
        text: "UserID",
        align: "center",
        sortable: false,
        value: "UserCode"
      },
      { text: "DeviceID", align: "center", value: "DeviceID" },
      { text: "LogTime", align: "center", value: "RecordDate" },
      { text: "SerialNumber", value: "RecordNumber" }
    ],

    loading: false,
    snackbar: false,
    response: "",

    url: process.env.SOCKET_ENDPOINT,
    Hash: process.env.Hash,
    logs: [],
    socket: null,

    msg: "",
    errors: []
  }),
  mounted() {
    this.socketConnection();
  },

  methods: {
    socketConnection() {
      this.socket = new WebSocket(this.url);

      this.socket.onmessage = ({ data }) => {
        let json = JSON.parse(data);
        if (json.Status == 200 && json.Data.UserCode !== 0) {
          this.getDetails(json.Data);
        }
      };
    },
    getDetails(item) {
      this.$axios.get(`/device/${item.DeviceID}/details`).then(({ data }) => {
        if (data.company_id == this.$auth.user.company.id) {
          this.logs.unshift(item);
        }
      });
    },

    store(payload) {
      let config = {
        headers: {
          Authorization: "Bearer " + this.Hash
        }
      };

      this.$axios.post("generate_log", payload, config).then(res => {});
    }
  }
};
</script>

<style scoped>
@media (min-width: 768px) {
  .gradient-form {
    height: 100vh !important;
  }
}

@media (min-width: 769px) {
  .primary {
    border-top-right-radius: 0.3rem;
    border-bottom-right-radius: 0.3rem;
  }
}
</style>
