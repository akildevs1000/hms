<template>
  <div>
    <v-row>
      <v-col md="4" class="text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large"></div>
            <div class="card-content">
              <h4 class="card-title text-capitalize py-1 white--text">
                Total Revenue
              </h4>
              <span class="data-1 white--text"> Rs {{ revenue }} </span>
              <p class="mb-0 text-sm">
                <span class="mr-2"> </span>
              </p>
            </div>
          </div>
        </div>
      </v-col>
      <v-col md="4" class="text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large"></div>
            <div class="card-content">
              <h4 class="card-title text-capitalize py-1 white--text">
                Total City Ledger
              </h4>
              <span class="data-1 white--text"> Rs {{ city_ledger }} </span>
              <p class="mb-0 text-sm">
                <span class="mr-2"> </span>
              </p>
            </div>
          </div>
        </div>
      </v-col>
      <v-col md="4" class="text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large"></div>
            <div class="card-content">
              <h4 class="card-title text-capitalize py-1 white--text">
                Number of Visit
              </h4>
              <span class="data-1 white--text">
                {{ bookings.length || "---" }}
              </span>
              <p class="mb-0 text-sm">
                <span class="mr-2"> </span>
              </p>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="12">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col" v-for="(item, index) in headers" :key="index">
                {{ item.text }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in bookings" :key="index">
              <th scope="row">
                <b>{{ ++index }}</b>
              </th>
              <td scope="row">{{ item.type || "---" }}</td>
              <td data-title="Released">
                {{ item.source || "---" }}
              </td>
              <td data-title="Studio">{{ item.rooms || "---" }}</td>
              <td data-title="Worldwide Gross" data-type="currency">
                {{ item.booking_date || "---" }}
              </td>
              <td data-title="Domestic Gross" data-type="currency">
                {{ item.check_in || "---" }}
              </td>
              <td data-title="International Gross" data-type="currency">
                {{ item.check_out || "---" }}
              </td>
              <td data-title="Budget" data-type="currency">
                {{ item.total_price || "---" }}
              </td>
            </tr>
          </tbody>
        </table>
      </v-col>
      <!-- <v-col cols="12" class="text-right">
        <v-btn x-small @click="prevTab" dark color="background">
          Back
        </v-btn>
      </v-col> -->
    </v-row>
  </div>
</template>

<script>
export default {
  props: ["customerId"],
  data() {
    return {
      customer: "",
      bookings: "",
      revenue: "",
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      loading: false,

      headers: [
        {
          text: "#",
        },
        {
          text: "Type",
        },
        {
          text: "Source",
        },
        {
          text: "Rooms",
        },
        {
          text: "Booking Date",
        },
        {
          text: "Check In",
        },
        {
          text: "Check Out",
        },
        {
          text: "Total Price",
        },
      ],
    };
  },
  watch: {
    customerId() {
      this.get_customer_history();
    },
  },
  mounted() {
    this.get_customer_history();
  },

  methods: {
    get_customer_history() {
      this.$axios
        .get(`get_customer_history/${this.customerId}`)
        .then(({ data }) => {
          this.customer = data.data;
          this.bookings = data.data.bookings;
          this.revenue = data.revenue;
          this.city_ledger = data.city_ledger;
          this.loading = false;
        });
    },
  },
};
</script>

<style scoped>
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
