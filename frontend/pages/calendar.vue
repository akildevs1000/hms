<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog v-model="checkInDialog" persistent max-width="700px">
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
          <v-btn
            class="primary"
            small
            @click="store_check_in(checkData)"
            :loading="loading"
            >Save</v-btn
          >
          <v-btn class="error" small @click="close"> Cancel </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="cancelDialog" persistent max-width="500">
      <v-card>
        <v-card-title class="text-h6">
          Are you sure you want to cancel this
        </v-card-title>
        <v-container grid-list-xs>
          <v-textarea
            placeholder="Reason"
            rows="3"
            dense
            outlined
            v-model="reason"
          ></v-textarea>
        </v-container>
        <v-card-actions>
          <v-btn class="primary" small :loading="loading" @click="cancelItem">
            Yes
          </v-btn>
          <v-btn class="error" small @click="cancelDialog = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="postingDialog" persistent max-width="700px">
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
                <th>Bill No</th>
                <td style="width:300px">
                  {{ evenIid || "---" }}
                </td>
              </tr>
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
              <tr style="background-color:white">
                <th>
                  Item
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="text"
                    v-model="posting.item"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr style="background-color:white">
                <th>
                  QTY
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="posting.qty"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr style="background-color:white">
                <th>
                  Amount
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="posting.amount"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr></tr>
            </table>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn class="primary" small @click="store_posting" :loading="loading"
            >submit</v-btn
          >
          <v-btn class="error" small @click="postingDialog = false">
            Cancel
          </v-btn>
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
            <v-list-item link @click="checkInDialog = true">
              <v-list-item-title>Check In</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="checkOutDialog = true">
              <v-list-item-title>Check Out</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="cancelDialog = true">
              <v-list-item-title>Cancel Room</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="postingDialog = true">
              <v-list-item-title>Posting</v-list-item-title>
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
      checkInDialog: false,
      postingDialog: false,
      cancelDialog: false,
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
          // { id: "103", room_no: "103", room_type: "king", eventColor: "green" },
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
            // console.log("contextmenu", eventId);
          });
        }
      },
      errors: [],
      data: [],
      checkData: {},
      evenIid: "",
      reason: "",
      posting: {
        item: "",
        qty: "",
        amount: ""
      }
    };
  },
  created() {},

  mounted() {
    this.room_list();
    this.get_events();
  },

  watch: {
    checkInDialog() {
      this.formTitle = "Check In";
      this.get_data();
    },

    postingDialog() {
      this.formTitle = "Posting";
      this.get_data();
    }
  },

  methods: {
    show(id, jsEvent) {
      this.evenIid = id;
      console.log(this.evenIid);
      this.x = jsEvent.clientX;
      this.y = jsEvent.clientY;
      this.$nextTick(() => {
        this.showMenu = true;
      });
    },

    get_remaining(val) {
      let total = this.checkData.total_price;
      let advance_price = val;
      this.checkData.remaining_price = total - advance_price;
    },

    room_list() {
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

    get_data() {
      let payload = {
        params: {
          id: this.evenIid
        }
      };
      this.$axios.get(`get_booking_by_check_in`, payload).then(({ data }) => {
        this.checkData = data;
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

    store_check_in(data) {
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
            this.succuss(data, true, false);
          }
        })
        .catch(e => console.log(e));
    },

    store_posting() {
      let rule =
        Object.keys(this.posting.item).length == 0 ||
        Object.keys(this.posting.amount).length == 0 ||
        Object.keys(this.posting.qty).length == 0;

      if (rule) {
        alert("Please enter required fields");
        return;
      }
      this.loading = true;
      let payload = {
        ...this.posting,
        booking_id: this.evenIid
      };
      console.log(payload);
      this.$axios
        .post("/posting", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, true);
          }
        })
        .catch(e => console.log(e));
    },

    cancelItem() {
      if (this.reason == "") {
        alert("Enter reason");
        return;
      }

      let payload = {
        reason: this.reason,
        cancel_by: this.$auth.user.id
      };
      this.$axios
        .post(`cancel_reservation/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.get_events();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch(err => console.log(err));
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
      this.checkInDialog = false;
    },

    succuss(data, check_in = false, posting = false) {
      if (check_in) {
        this.checkData = {};
        this.checkInDialog = false;
      }
      if (posting) {
        this.posting = {};
        this.postingDialog = false;
      }
      this.errors = [];
      this.loading = false;
      this.snackbar = true;
      this.response = data.message;
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
