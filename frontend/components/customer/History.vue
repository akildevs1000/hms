<template>
  <div>
    <v-row class="my-2 mx-0">
      <v-col cols="12" md="6" lg="4" xl="4" class="py-0">
        <v-card class="mx-1 elevation-0">
          <v-row class="box text-center no-of-visit-txt" style="background-color: #02ada4;height: 81px;">
            <v-col md="6">
              <h1 class="font-light text-white">
                <PaidBookedSvg />
                <h4 class="pb-0 mb-0 text-left white--text
                                                              dash-font-size ipad-font-grid ipad-font-paid-grid
                                                               laptop-font-paid-grid"
                  style="  margin: -15px 0px 0px 0px;   ">
                  Revenue
                </h4>
              </h1>
            </v-col>
            <v-col md="6">
              <h1 class="big-screen laptop-font-grid ipad-font-qty-grid mt-4">
                {{ revenue }}
              </h1>
            </v-col>
          </v-row>
        </v-card>
      </v-col>


      <!-- <v-col cols="12" md="6" xl="3">
        <v-card class="px-2" color="#800000">
          <v-card-text class="text-center white--text">
            <h6 class="text-uppercase">evenue</h6>
            <div class="display-1 mb-2">₹{{ revenue || 0 }}</div>
          </v-card-text>
        </v-card>
      </v-col> -->



      <v-col cols="12" lg="4" md="6" xl="4" class="py-0">
        <v-card class="mx-1 elevation-0">
          <v-row class="box text-center no-of-visit-txt" style="background-color: #4390fc;height: 81px;">
            <v-col md="6">
              <h1 class="font-light text-white">
                <PaidBookedSvg />
                <h4
                  class="pb-0 mb-0 text-left white--text dash-font-size ipad-font-grid ipad-font-paid-grid laptop-font-paid-grid"
                  style="  margin: -15px 0px 0px 0px;   ">
                  City Ledger
                </h4>
              </h1>
            </v-col>
            <v-col md="6">
              <h1 class="big-screen laptop-font-grid ipad-font-qty-grid mt-4">
                {{ city_ledger }}
              </h1>
            </v-col>
          </v-row>
        </v-card>
      </v-col>

      <v-col cols="12" lg="4" md="12" xl="4" class="py-0 no-of-visit-div">
        <v-card class="mx-1 elevation-0">
          <v-row class="box text-center no-of-visit-txt-vis" style="background-color: #ffbe00;height: 81px;">
            <v-col md="6">
              <h1 class="font-light text-white" style="    margin-top: -10px;  ">
                <Available />
                <h4
                  class="pb-0 mb-0 white--text text-left mt-3 dash-font-size ipad-font-grid ipad-font-paid-grid no-of-visit"
                  style="  margin: -15px 0px 0px 0px;   ">
                  Number of Visit
                </h4>
              </h1>
            </v-col>
            <v-col md="6">
              <h1 class="big-screen laptop-font-grid ipad-font-qty-grid mt-4">
                {{ bookings.length || "0" }}
              </h1>
            </v-col>
          </v-row>
        </v-card>
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
              <td scope="row">
                <span class="blue--text" @click="goToRevView(item)" style="cursor: pointer;">
                  {{ (item.reservation_no) || "---" }}
                </span>
              </td>
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
import PaidBookedSvg from "../svg/PaidBookedSvg.vue";
import Available from "../svg/Available.vue";
export default {
  components: {
    Available,
    PaidBookedSvg,
  },
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
      viewportWidth: 0,
      viewportHeight: 0,
      headers: [
        {
          text: "#",
        },
        {
          text: "Rev. No",
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



    this.viewportWidth = window.innerWidth;
    this.viewportHeight = window.innerHeight;

    window.addEventListener('resize', () => {
      this.viewportWidth = window.innerWidth;
      this.viewportHeight = window.innerHeight;
    });

  },
  methods: {
    goToRevView(item) {
      this.$router.push(`/customer/details/${item.id}`);
    },

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

.dash-font-size {
  font-size: 13px;
}

.big-screen {
  font-size: 25px;
  color: white;
}

.food-icon-size {
  font-size: 30px !important;
}

@media only screen and (min-width: 1025px) and (max-width: 1199px) {

  /* Adjust layout for iPad pro landscape mode */
  .ipad-font-grid {
    font-size: 12px !important;
    color: white;
    margin-top: 10px !important;
  }
}

@media only screen and (min-width: 1366px) and (max-width: 1366px) and (min-height: 768px) and (max-height: 768px) {
  .laptop-font-grid {
    font-size: 15px !important;
    color: white !important;
    margin-top: 7px !important;
  }

  .no-of-visit {
    margin-top: 24px !important;
    font-size: 11px !important;
  }

  .laptop-font-paid-grid {
    font-size: 11px !important;
    color: white;
    margin-top: 17px !important;
    font-weight: bold;
  }

  .available-room-list {
    width: 13.333333% !important;
  }
}

@media only screen and (min-width: 1024px) and (max-width: 1024px) and (min-height: 768px) and (max-height: 768px) {

  /* ipad mini Air */
  .ipad-font-qty-grid {
    font-size: 55px !important;
    color: white;
  }

  .ipad-font-paid-grid {
    font-size: 11px !important;
    color: white;
    margin-top: 17px !important;
    font-weight: bold;
  }

  .ipad-font-food-grid {
    color: red !important;
  }

  .available-room-list {
    width: 13.333333% !important;
  }

  .no-of-visit-div {
    margin-top: 100px
  }
}



@media only screen and (min-width: 1112px) and (max-width: 1112px) and (min-height: 764px) and (max-height: 764px) {
  .no-of-visit-div {
    margin-top: 30px
  }

  .no-of-visit-txt {
    height: 120px !important;
  }

  .no-of-visit-txt-vis {
    height: 150px !important;
  }
}
</style>
