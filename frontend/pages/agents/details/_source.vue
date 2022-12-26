<template>
  <div>
    <v-row>
      <v-col md="3">
        <v-card class="pa-5 mt-0" elevation="0">
          <h5>Agents</h5>
          <hr />
          <table>
            <tbody>
              <tr>
                <th class="no-bg">Source Type</th>
                <td class="no-bg">
                  {{ agent.type || "---" }}
                </td>
              </tr>
              <tr>
                <th class="no-bg">Source Name</th>
                <td class="no-bg">
                  {{ agent.source || "---" }}
                </td>
              </tr>
            </tbody>
          </table>
        </v-card>
      </v-col>
      <v-col md="9">
        <v-card class="mb-5 rounded-md " elevation="0">
          <table>
            <tr style="font-size:15px">
              <th v-for="(item, index) in headers" :key="index">
                {{ item.text }}
              </th>
            </tr>
            <v-progress-linear
              v-if="false"
              :active="loading"
              :indeterminate="loading"
              absolute
              color="primary"
            ></v-progress-linear>
            <tr
              style="font-size:12px"
              v-for="(item, index) in data"
              :key="index"
            >
              <td>
                <b>{{ ++index }}</b>
              </td>
              <td>{{ item.booking_id || "---" }}</td>
              <td>{{ item.reference_no || "---" }}</td>
              <td>
                {{
                  (item && item.customer && item.customer.full_name) || "---"
                }}
              </td>
              <td>{{ item.amount || "---" }}</td>
              <td>
                {{ (item && item.booking && item.booking.rooms) || "---" }}
              </td>
              <td>
                <v-chip
                  class="ma-2"
                  :color="item.is_paid == 1 ? 'green' : 'red'"
                  text-color="white"
                  small
                >
                  {{ item.is_paid == 1 ? "Paid" : "Pending" }}
                </v-chip>
              </td>
              <td>{{ item.paid_date || "---" }}</td>
              <td>{{ item.payment_mode || "---" }}</td>
              <td>
                {{
                  (item && item.booking && item.booking.check_in_date) || "---"
                }}
              </td>
              <td>
                {{
                  (item && item.booking && item.booking.check_out_date) || "---"
                }}
              </td>
              <td>
                {{
                  (item && item.booking && item.booking.booking_date) || "---"
                }}
              </td>
            </tr>
          </table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    pagination: {
      current: 1,
      total: 0,
      per_page: 10
    },
    options: {},
    Model: "Customer",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    response: "",
    data: [],
    agent: {},
    customer: [],
    payments: [],
    booking: [],
    bookedRooms: [],
    errors: [],

    headers: [
      {
        text: "#"
      },
      {
        text: "Booking Number"
      },
      {
        text: "Reference Number"
      },
      {
        text: "Customer"
      },
      {
        text: "Amount"
      },
      {
        text: "Rooms"
      },
      {
        text: "Payment Status"
      },
      {
        text: "Paid Date"
      },
      {
        text: "Payment Mode"
      },
      {
        text: "Check In"
      },
      {
        text: "Check Out"
      },
      {
        text: "Booking Date"
      }
    ]
  }),

  computed: {},
  created() {
    this.loading = true;
    this.getData();
  },
  mounted() {},

  methods: {
    getDate(dataTime) {
      return dataTime;
      // return new Date(dataTime.toDateString());
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    getData() {
      let source = this.$route.params.source;
      this.$axios
        .get(`get_agent_details`, {
          params: { source: source, company_id: this.$auth.user.company.id }
        })
        .then(({ data }) => {
          if (data.status) {
            this.data = data.data;
            this.agent = data.data[0];
            console.log(data.data);
            this.booking = data.booking;
            this.payments = data.booking.payments;
            this.bookedRooms = data.booking.booked_rooms;
            this.loading = false;
          }
        });
    }
  }
};
</script>

<style scoped>
.no-bg {
  background-color: white !important;
}

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.custom-text-box {
  border-radius: 2px !important;
  border: 1px solid #dbdddf !important;
}
input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select:focus {
  outline: none !important;
  border-color: #5fafa3;
  box-shadow: 0 0 0px #5fafa3;
}
</style>
