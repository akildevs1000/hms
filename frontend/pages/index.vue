<template>
  <div v-if="!loading">
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
            <v-list-item
              v-if="eventStatus == 1"
              link
              @click="checkInDialog = true"
            >
              <v-list-item-title>Check In</v-list-item-title>
            </v-list-item>

            <v-list-item
              v-else-if="eventStatus == 2"
              link
              @click="checkOutDialog = true"
            >
              <v-list-item-title>Check Out</v-list-item-title>
            </v-list-item>

            <v-list-item
              v-else-if="eventStatus == 3"
              link
              @click="setAvailable"
            >
              <v-list-item-title>Make Available</v-list-item-title>
            </v-list-item>

            <div v-if="isDirty">
              <v-list-item link @click="payingAdvance = true">
                <v-list-item-title>Pay Advance</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="cancelDialog = true">
                <v-list-item-title>Cancel Room</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="postingDialog = true">
                <v-list-item-title>Posting</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="viewPostingDialog = true">
                <v-list-item-title>View Posting</v-list-item-title>
              </v-list-item>
            </div>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>

    <div v-html="temp"></div>

    <v-row>
      <v-col md="12"> </v-col>
      <div
        class="col-xl-2 col-lg-6 text-uppercase"
        v-for="(i, index) in items"
        :key="index"
      >
        <div v-if="index < 6" class="card p-2" :class="i.color">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large ">
              <i :class="i.icon"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">
                {{ i.title }} {{ index }}
              </h4>
              <span class="data-1"> {{ i.value }}</span>
              <p class="mb-0 text-sm">
                <span class="mr-2"
                  ><v-icon dark small>mdi-arrow-right</v-icon></span
                >
                <a
                  class="text-nowrap text-white"
                  target="_blank"
                  :href="i.link"
                >
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </v-row>
    <v-row>
      <v-col md="9" sm="12" cols="12">
        <v-card class="pa-5 mt-1">
          <h6>Rooms</h6>
          <v-row>
            <v-col
              :class="room.id"
              md="2"
              sm="12"
              cols="12"
              v-for="(room, index) in rooms"
              :key="index"
            >
              <v-card
                @contextmenu="show"
                :elevation="0"
                class="ma-1 pa-5"
                dark
                :class="getRelaventColor(room.status)"
                ><div class="text-center">{{ caps(room.room_type) }}</div>
                <div class="text-center">
                  {{ room.room_no }}
                </div>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col md="3" sm="12" cols="12">
        <div class=" text-uppercase" v-for="(i, index) in items" :key="index">
          <div v-if="index == 6" class="card p-2" :class="i.color">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large ">
                <i :class="i.icon"></i>
              </div>
              <div class="card-content">
                <h4 class="card-title text-capitalize">
                  {{ i.title }} {{ index }}
                </h4>
                <span class="data-1"> {{ i.value }}</span>
                <p class="mb-0 text-sm">
                  <span class="mr-2"
                    ><v-icon dark small>mdi-arrow-right</v-icon></span
                  >
                  <a
                    class="text-nowrap text-white"
                    target="_blank"
                    :href="i.link"
                  >
                    <span class="text-nowrap">View Report</span>
                  </a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>
  </div>
  <Preloader v-else />
</template>
<script>
export default {
  data() {
    return {
      temp: "",
      loading: false,
      snackbar: false,
      response: "",
      isDirty: true,
      payingAdvance: false,
      checkInDialog: false,
      checkOutDialog: false,
      postingDialog: false,
      viewPostingDialog: false,
      cancelDialog: false,
      formTitle: "",
      selectedItem: 0,
      showMenu: false,
      x: 0,
      y: 0,

      elevations: [6, 12, 18],
      first_login_auth: 1,
      loading: true,

      logs: [],

      total_items: [],
      items_by_cities: [],
      test_headers: [
        {
          text: "Customer",
          align: "left",
          sortable: false,
          value: "company_name"
        },
        {
          text: "Order Total",
          align: "left",
          sortable: false,
          value: "order_total"
        }
      ],
      orders: "",
      products: "",
      customers: "",
      daily_orders: "",
      weekly_orders: "",
      monthly_orders: "",
      evenIid: "",
      eventStatus: "",
      rooms: [],
      items: [],
      chartData: [
        ["Month", "On Time", "Absence", "Lates"],
        ["Apr", 33, 4, 7],
        ["Mar", 17, 6, 3],
        ["Feb", 41, 9, 1]
      ],
      chartOptions: {
        chart: {
          title: "Company Performance",
          subtitle: "Sales, Expenses, and Profit: 2014-2017"
        }
      },
      BublechartOptions: {
        colorAxis: { colors: ["yellow", "red"] }
      }
    };
  },
  created() {
    this.room_list();
    this.initialize();
    this.first_login_auth = this.$auth.user.first_login;
    // this.classObject();
  },
  computed: {
    first_login() {
      return this.$store.state.first_login;
    },
    color() {
      // reservation booked available checkedIn checkedOut
      return "reservation";
    }
  },
  filters: {
    get_decimal_value: function(value) {
      if (!value) return "";
      return (Math.round(value * 100) / 100).toFixed(2);
    },
    get_comma_seperator: function(x) {
      if (!x) return "";
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
  },
  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    },

    show(e) {
      let string = JSON.stringify(e.path[2]);
      let obj = JSON.parse(string);
      let str = obj._prevClass;
      const room_no = str.split(" ").pop();

      if (room_no == "row") {
        return;
      }

      // this.evenIid = id;
      // this.eventStatus = eventStatus;
      // if (this.eventStatus == 3) {
      //   this.isDirty = false;
      // }

      console.log(room_no);

      e.preventDefault();

      this.x = e.clientX;
      this.y = e.clientY;
      this.$nextTick(() => {
        this.showMenu = true;
      });
    },

    room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`room_list`, payload).then(({ data }) => {
        this.rooms = data;
      });
    },
    caps(str) {
      return str.replace(/\b\w/g, c => c.toUpperCase());
    },
    getRelaventColor(status) {
      switch (parseInt(status)) {
        case 1:
          return "booked";
        case 2:
          return "checkedIn";
        case 3:
          return "checkedOut";
        case 4:
          return "background";
        case 5:
          return "grey";
        default:
          return "available";
      }
    },
    initialize() {
      let options = {
        company_id: this.$auth.user.company.id
      };
      this.$axios.get(`count`, { params: options }).then(({ data }) => {
        this.items = data;

        if (this.items.length > 0) {
          this.loading = false;
        }
      });
    }
  }
};
</script>

<style scoped src="@/assets/dashtem.css"></style>
