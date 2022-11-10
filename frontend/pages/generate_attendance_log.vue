<template>
  <div v-if="can(`user_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-10">
      <v-col cols="10">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>
    <v-card>
      <v-toolbar dark flat class="primary">
        <h3>{{ Model }}</h3>
      </v-toolbar>

      <v-card flat>
        <v-card-text>
          <v-row>
            <v-col md="6">
              <v-text-field
                v-model="payload.user_id"
                label="User Id"
              ></v-text-field>
              <span v-if="errors && errors.user_id" class="text-danger mt-2">{{
                errors.user_id[0]
              }}</span>
            </v-col>
            <v-col md="6">
              <v-text-field
                v-model="payload.device_id"
                label="Device Id"
              ></v-text-field>
              <span
                v-if="errors && errors.device_id"
                class="text-danger mt-2"
                >{{ errors.device_id[0] }}</span
              >
            </v-col>

            <v-col md="6">
              <v-menu
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                :return-value.sync="date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="payload.date"
                    label="Date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="payload.date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.menu.save(payload.date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>

            <v-col md="6">
              <v-menu
                ref="time_menu_ref"
                v-model="time_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="payload.time"
                    label="Time In"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="time_menu"
                  v-model="payload.time"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn x-small color="primary" @click="time_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="$refs.time_menu_ref.save(payload.time)"
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span v-if="errors && errors.time" class="text-danger mt-2">{{
                errors.time[0]
              }}</span>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <div class="text-right">
                <v-btn
                  v-if="can(`user_create`)"
                  small
                  :loading="loading"
                  color="primary"
                  @click="store_schedule"
                >
                  Submit
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    Model: "Generate Log",

    date: null,
    menu: false,

    loading: false,
    time_menu: false,

    payload: {
      user_id: null,
      device_id: null,
      date: null,
      time: null
    },

    errors: [],
    data: [],
    response: "",
    snackbar: false
  }),
  async created() {},
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    store_schedule() {
      let { user_id, date, time, device_id } = this.payload;
      let payload = {
        UserID: user_id,
        LogTime: date + " " + time + ":00",
        DeviceID: device_id,
        company_id: this.$auth.user.company.id
      };
      this.loading = true;

      this.$axios
        .post(`/generate_log`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "Log generate successfully";
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    }
  }
};
</script>
