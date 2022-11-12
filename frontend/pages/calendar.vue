<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="CheckInDialog" persistent max-width="700px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }}</span>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <table>
              <v-progress-linear
                v-if="loading"
                :active="loading"
                :indeterminate="loading"
                absolute
                color="primary"
              ></v-progress-linear>
              <tr>
                <th>Customer Name</th>
                <td style="width:300px">
                  {{ checkData && checkData.title }}
                </td>
              </tr>
              <tr>
                <th>Room No</th>
                <td>
                  {{ checkData && checkData.room && checkData.room.room_no }}
                </td>
              </tr>
              <tr>
                <th>Room Type</th>
                <td>
                  {{
                    checkData && checkData.room && checkData.room.room_type.name
                  }}
                </td>
              </tr>
              <tr>
                <th>Check In</th>
                <td>
                  {{ checkData && checkData.check_in }}
                </td>
              </tr>
              <tr>
                <th>Check Out</th>
                <td>
                  {{ checkData && checkData.check_out }}
                </td>
              </tr>
              <tr>
                <th>
                  Payment Mode
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-select
                    v-model="checkData.payment_mode_id"
                    :items="[
                      { id: 1, name: 'Cash' },
                      { id: 2, name: 'Card' },
                      { id: 3, name: 'Online' },
                      { id: 4, name: 'Bank' },
                      { id: 5, name: 'UPI' },
                      { id: 6, name: 'Cheque' }
                    ]"
                    item-text="name"
                    item-value="id"
                    dense
                    outlined
                    :hide-details="true"
                    :height="1"
                  ></v-select>
                </td>
              </tr>
              <tr>
                <th>Total Amount</th>
                <td>
                  {{ checkData && checkData.total_price }}
                </td>
              </tr>
              <tr></tr>
              <tr>
                <th>
                  Advance Price
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="checkData.advance_price"
                    :hide-details="true"
                    @keyup="get_remaining(checkData.advance_price)"
                  ></v-text-field>
                </td>
              </tr>
              <tr></tr>
              <tr>
                <th>Remaining Balance</th>
                <td>
                  {{ checkData.remaining_price }}
                </td>
              </tr>
              <tr></tr>
            </table>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="error" small @click="close"> Cancel </v-btn>
          <v-btn
            class="primary"
            small
            @click="store(checkData)"
            :loading="loading"
            >Save</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <div>
      <v-row class="flex" justify="center"> </v-row>
      <v-menu
        v-model="showMenu"
        :position-x="x"
        :position-y="y"
        absolute
        offset-y
      >
        <v-list>
          <v-list-item-group v-model="selectedItem">
            <v-list-item link>
              <v-list-item-title @click="check_in">check in</v-list-item-title>
            </v-list-item>
            <v-list-item link>
              <v-list-item-title>check Out</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>
    <v-row>
      <v-col cols="12">
        <FullCalendar :options="calendarOptions" style="background:#fff;" />
      </v-col>
    </v-row>
  </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";

export default {
  components: {
    FullCalendar
  },
  data() {
    return {
      loading: false,
      snackbar: false,
      response: "",
      CheckInDialog: false,
      formTitle: "",
      selectedItem: 0,
      showMenu: false,
      x: 0,
      y: 0,
      calendarOptions: {
        plugins: [interactionPlugin, dayGridPlugin, resourceTimelinePlugin],
        now: "2022-11-07",
        editable: true,
        aspectRatio: 1.8,
        scrollTime: "00:00",
        displayEventTime: false,

        initialView: "resourceTimelineMonth",

        navLinks: true,
        resourceAreaWidth: "15%",

        resourceAreaColumns: [
          {
            headerContent: "Room",
            field: "room_no"
          },
          {
            headerContent: "Room Type",
            field: "room_type"
          }
        ],
        resources: [
          // { id: "103", room_no: "103", eventColor: "green" },
          // { id: "104", room_no: "104", eventColor: "orange" }
        ],
        events: [
          // {
          //   id: "1",
          //   room_id: "1",
          //   resourceId: "104",
          //   start: "2022-11-09 00:00:00",
          //   end: "2022-11-11 06:00:00",
          //   title: "e"
          // },
          // {
          //   id: "1",
          //   room_id: "1",
          //   resourceId: "103",
          //   start: "2022-11-09 00:00:00",
          //   end: "2022-11-11 06:00:00",
          //   title: "e"
          // }
        ],
        eventDidMount: arg => {
          const eventId = arg.event.id;

          arg.el.addEventListener("contextmenu", jsEvent => {
            this.show(eventId, jsEvent);
            jsEvent.preventDefault();

            console.log("contextmenu", eventId);
          });
        }
      },
      errors: [],
      data: [],
      checkData: {},
      evenIid: ""
    };
  },
  created() {},

  mounted() {
    this.room_types();
    this.get_events();
  },

  watch: {
    CheckInDialog() {
      this.formTitle = "Check In";
      let payload = {
        params: {
          id: this.evenIid
        }
      };
      console.log(payload);
      this.$axios.get(`get_booking_by_check_in`, payload).then(({ data }) => {
        this.checkData = data;
        console.log(this.checkData);
      });
    }
  },

  methods: {
    show(id, jsEvent) {
      this.evenIid = id;
      this.x = jsEvent.clientX;
      this.y = jsEvent.clientY;
      this.$nextTick(() => {
        this.showMenu = true;
      });
    },
    check_in() {
      this.CheckInDialog = true;
    },
    get_remaining(val) {
      let total = this.checkData.total_price;
      let advance_price = val;
      this.checkData.remaining_price = total - advance_price;
    },
    room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`room_list`, payload).then(({ data }) => {
        this.calendarOptions.resources = data;
      });
    },

    get_events() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`events_list`, payload).then(({ data }) => {
        this.calendarOptions.events = data;
      });
    },

    validate_payment() {
      if (
        this.checkData.advance_price == 0 ||
        this.checkData.advance_price == ""
      ) {
        alert("enter advance_price amount");
        return true;
      }
      if (this.checkData.payment_mode_id == "") {
        alert("select payment mode");
        return true;
      }
      return false;
    },

    store(data) {
      if (this.validate_payment()) {
        return;
      }
      this.loading = true;
      let payload = {
        booking_id: this.evenIid,
        advance_price: data.advance_price,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id
      };
      this.$axios
        .post("/check_in_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.loading = false;
            this.CheckInDialog = false;
            this.snackbar = true;
            this.response = data.message;
          }
        })
        .catch(e => console.log(e));
    },

    update_by_drag(payload) {
      alert(payload);

      return;
      this.$axios
        .post("/booking", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.message;
            this.response = data.message;
          }
        })
        .catch(e => console.log(e));
    },
    close() {
      this.CheckInDialog = false;
    }
  }
};
</script>

<style scoped>
.app {
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}

.portrait.v-card {
  margin: 0 auto;
  max-width: 600px;
  width: 100%;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #e9e9e9;
}
td,
th {
  text-align: left;
  padding: 8px;
  border: 1px solid #e9e9e9;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
