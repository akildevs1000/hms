<template>
  <div>
    <v-skeleton-loader v-if="logs && !logs.length" type="card" />
    <div v-else>
      <v-toolbar flat>
        <h5>
          <b>
            Recent Logs
          </b>
        </h5>
        <v-spacer />
        <v-select
          @change="getRecords"
          v-model="number_of_records"
          outlined
          dense
          class="mt-5"
          placeholder="Select Number of Records"
          :items="[10, 20, 50, 100]"
          style="max-width: 200px !important;"
        ></v-select>
      </v-toolbar>
      <v-slide-group class="px-4 pb-3" active-class="success" show-arrows>
        <div></div>
        <v-slide-item v-for="(item, index) in logs" :key="index">
          <div class="card mx-2 my-2 w-25">
            <div class="banner">
              <v-img
                class="gg"
                viewBox="0 0 100 100"
                style="border-radius: 50%;  height: 80px; max-width: 80px !important"
                :src="
                  (item.employee && item.employee.profile_picture) ||
                    '/no-profile-image.jpg'
                "
              ></v-img>
            </div>
            <div class="menu">
              <div class="opener"></div>
            </div>
            <h2 class="text-center pa-1" style="font-size:15px">
              {{ item.employee && item.employee.first_name }}
            </h2>
            <div class="title" style="font-size:12px !important">
              EID: {{ item.UserID }}
            </div>
            <div class="title" style="font-size:12px !important"></div>
            <div class="actions">
              <div class="follow-info">
                <h2>
                  <a href="#"
                    ><span>{{ item && item.time }} </span><small>Time</small></a
                  >
                </h2>
                <h2>
                  <a href="#"
                    ><span>{{
                      (item.device && item.device.short_name) || "---"
                    }}</span
                    ><small>Device</small></a
                  >
                </h2>
              </div>
            </div>
          </div>
        </v-slide-item>
      </v-slide-group>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      number_of_records: 10,
      logs: [],
      url: process.env.SOCKET_ENDPOINT,
      socket: null
    };
  },
  mounted() {
    this.socketConnection();

    this.getRecords();
  },
  created() {
    // this.getRecords();
  },
  methods: {
    getRecords() {
      this.$axios
        .get(
          `device/getLastRecordsByCount/${this.$auth.user.company.id}/${this.number_of_records}`
        )
        .then(res => {
          this.logs = res.data;
        });
    },
    getShortName(item) {
      if (!item) {
        return false;
      }
      return item
        .split(" ")
        .slice(0, 2)
        .join(" ");
    },
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
      this.$axios.post(`/device/details`, item).then(({ data }) => {
        if (data.device.company_id == this.$auth.user.company.id) {
          this.logs.unshift(data);
        }
      });
    }
  }
};
</script>

<style scoped>
.card {
  height: 350px !important;
  display: flex !important;
  flex-direction: column !important;
  overflow: hidden !important;
  border-radius: 2rem !important;
}
.card .banner {
  background-color: #5fafa3;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  height: 11rem;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  box-sizing: border-box;
}
.card .banner .gg {
  background-color: #fff;
  width: 8rem;
  height: 8rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  transform: translateY(50%);
  transition: transform 200ms cubic-bezier(0.18, 0.89, 0.32, 1.28);
}
.card .banner svg:hover {
  transform: translateY(50%) scale(1.3);
}
.card .menu {
  width: 100%;
  height: 5.5rem;
  padding: 1rem;
  display: flex;
  align-items: flex-start;
  justify-content: flex-end;
  position: relative;
  box-sizing: border-box;
}
.card .menu .opener {
  width: 2.5rem;
  height: 2.5rem;
  position: relative;
  border-radius: 50%;
  transition: background-color 100ms ease-in-out;
}
.card .menu .opener:hover {
  background-color: #f2f2f2;
}
.card .menu .opener span {
  background-color: #404040;
  width: 0.4rem;
  height: 0.4rem;
  position: absolute;
  top: 0;
  left: calc(50% - 0.2rem);
  border-radius: 50%;
}
.card .menu .opener span:nth-child(1) {
  top: 0.45rem;
}
.card .menu .opener span:nth-child(2) {
  top: 1.05rem;
}
.card .menu .opener span:nth-child(3) {
  top: 1.65rem;
}

.card .title {
  color: #a0a0a0;
  font-size: 0.85rem;
  text-align: center;
  /* padding: 0 2rem 1.2rem; */
}
.card .actions {
  padding: 0 2rem 1.2rem;
  display: flex;
  flex-direction: column;
  order: 99;
}
.card .actions .follow-info {
  padding: 0 0 1rem;
  display: flex;
}
.card .actions .follow-info h2 {
  text-align: center;
  width: 50%;
  margin: 0;
  box-sizing: border-box;
}
.card .actions .follow-info h2 a {
  text-decoration: none;
  padding: 0.8rem;
  display: flex;
  flex-direction: column;
  border-radius: 0.8rem;
  transition: background-color 100ms ease-in-out;
}
.card .actions .follow-info h2 a span {
  color: #1c9eff;
  font-weight: bold;
  transform-origin: bottom;
  transform: scaleY(1.3);
  transition: color 100ms ease-in-out;
  font-size: 20px;
}
.card .actions .follow-info h2 a small {
  color: #afafaf;
  font-size: 0.85rem;
  font-weight: normal;
}
.card .actions .follow-info h2 a:hover {
  background-color: #f2f2f2;
}
.card .actions .follow-info h2 a:hover span {
  color: #007ad6;
}
</style>
