<template>
  <div v-if="can('devices_permissions_access') && can('devices_view')">
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top="top"
        :color="snackbarColor"
        elevation="24"
      >
        {{ snackbarResponse }}
      </v-snackbar>
    </div>
    <v-card>
      <v-card-text>
        <v-container>
          <v-icon
            color="black"
            class="pull-right"
            style="float: right"
            @click="getDataFromApi()"
            dark
            >mdi mdi-reload</v-icon
          ><br />
          {{ deviceSettings.error }}
          <v-row v-if="deviceSettings.error">
            <v-col style="color: red">{{ deviceSettings.error }}</v-col>
          </v-row>
          <v-row v-if="deviceSettings.config">
            <v-col md="12" cols="12">
              <v-text-field
                :disabled="viewMode"
                v-model="deviceSettings.config.serverURL"
                outlined
                dense
                small
                :hide-details="true"
                label="Server URL"
              ></v-text-field>
              <!-- <span
                dense
                v-if="errors && errors.server_url"
                class="error--text"
                >{{ errors.server_url[0] }}</span
              > -->
            </v-col>
            <v-col md="12" cols="12">
              <v-text-field
                :disabled="viewMode"
                v-model="deviceSettings.config.intervalHeartbeat"
                outlined
                dense
                small
                type="number"
                :hide-details="true"
                label="Heartbeat"
              ></v-text-field>
              <!-- <span v-if="errors && errors.intervalHearbeat" class="error--text">{{
                errors.intervalHearbeat[0]
              }}</span> -->
            </v-col>
            <v-col md="12" cols="12">
              <v-text-field
                :disabled="viewMode"
                v-model="deviceSettings.config.server_ip"
                outlined
                dense
                small
                :hide-details="true"
                label="Socket IP Address"
              ></v-text-field>
              <!-- <span v-if="errors && errors.socket_ip" class="error--text">{{
                errors.socket_ip[0]
              }}</span>
               -->
            </v-col>
            <v-col md="12" cols="12">
              <v-text-field
                :disabled="viewMode"
                v-model="deviceSettings.config.server_port"
                outlined
                dense
                small
                number
                :hide-details="true"
                label="Socket Port"
              ></v-text-field>
              <!-- <span v-if="errors && errors.socket_port" class="error--text">{{
                errors.socket_port[0]
              }}</span> -->
            </v-col>
            <v-col md="12" cols="12">
              <v-text-field
                :disabled="viewMode"
                v-model="deviceSettings.config.gmtTimeZone"
                outlined
                dense
                small
                number
                :hide-details="true"
                label="GMT Timezone"
              ></v-text-field>
              <!-- <span v-if="errors && errors.socket_port" class="error--text">{{
                errors.socket_port[0]
              }}</span> -->
            </v-col>
            <v-col md="12" cols="12">
              <v-text-field
                disabled
                v-model="deviceSettings.config.wifiSSID"
                outlined
                dense
                small
                number
                :hide-details="true"
                label="wifiSSID"
              ></v-text-field> </v-col
            ><v-col md="12" cols="12">
              <v-text-field
                disabled
                v-model="deviceSettings.config.wifiPassword"
                outlined
                dense
                small
                number
                :hide-details="true"
                label="wifiPassword"
              ></v-text-field>
            </v-col>
            <v-col md="12" cols="12">
              <v-text-field
                disabled
                v-model="deviceSettings.config.lastUpdated"
                outlined
                dense
                small
                number
                :hide-details="true"
                label="Config Updated On"
              ></v-text-field>
            </v-col>
            <v-col md="12" cols="12">
              <v-text-field
                disabled
                v-model="deviceSettings.last_heartbeat"
                outlined
                dense
                small
                number
                :hide-details="true"
                label="last Heartbeat"
              ></v-text-field>
            </v-col>
          </v-row>

          <v-card-actions class="mt-5" v-if="!viewMode">
            <v-btn @click="newItemDialog = false" dark filled color="red"
              >Cancel</v-btn
            >
            <v-spacer></v-spacer>
            <v-btn @click="save()" dark filled color="primary">Save</v-btn>
          </v-card-actions>
        </v-container>
      </v-card-text>
    </v-card>
  </div>

  <NoAccess v-else />
</template>
<script>
export default {
  props: ["addNew", "editedItem"],
  data: () => ({
    //datatable varables
    message: "",
    page: 1,
    perPage: 0,
    currentPage: 1,
    cumulativeIndex: 1,
    totalTableRowsCount: 0,
    options: {},
    filters: {},
    isFilter: false,
    data: [],
    loading: false,
    deviceSettings: { config: {} },
    headers_table: [
      {
        text: "#",
        value: "sno",
        align: "left",
        sortable: false,
        filterable: false,
      },
      {
        text: "Serial Number",
        value: "serial_number",
        align: "left",
        sortable: true,
        filterable: true,
      },
      {
        text: "Device Name",
        value: "name",
        key: "name",
        align: "left",
        sortable: true,
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Roo No",
        value: "room.room_no",
        align: "left",
        sortable: true,
        key: "room_id",
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Status",
        value: "latest_status",
        align: "left",
        sortable: true,
        key: "room_id",
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Status Time",
        value: "latest_status_time",
        align: "left",
        sortable: true,
        key: "room_id",
        filterable: true,
        filterSpecial: true,
      },

      { text: "Options", value: "options", align: "left", sortable: false },
    ],
    roomList: [],

    endpoint: "devices",

    newItemDialog: false,

    //add edit item details

    editedItemIndex: -1,
    roomTypesForSelectOptions: [],
    errors: {},
    snackbar: false,
    snackbarColor: "black",
    snackbarResponse: "",
    viewMode: false,
    floors: [
      1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
    ],
  }),

  created() {
    this.getDataFromApi();
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    getDataFromApi() {
      this.message = "loading....";
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          serial_number: this.editedItem.serial_number,
        },
      };
      this.deviceSettings = { config: {} };

      this.$axios.get(`device-settings`, options).then(({ data }) => {
        if (!data.error) {
          // Update the observer properties directly
          Object.assign(this.deviceSettings, data.message);
        } else {
          this.message = data.error;
        }
        console.log(this.deviceSettings);
      });
    },
    save() {
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          serial_number: this.editedItem.serial_number,
          gmtTimeZone: this.deviceSettings.config.gmtTimeZone,
          intervalHeartbeat: this.deviceSettings.config.intervalHeartbeat,
          serverURL: this.deviceSettings.config.serverURL,
          server_ip: this.deviceSettings.config.server_ip,
          server_port: this.deviceSettings.config.server_port,
        },
      };

      this.$axios
        .post(`device-settings-update`, options.params)
        .then(({ data }) => {
          this.snackbar = true;
          this.snackbarResponse = "Device Settings Updated successfully";
        });
    },
  },
};
</script>
