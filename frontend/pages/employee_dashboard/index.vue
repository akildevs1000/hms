<template>
  <div v-if="can(`employee_access`)">
    <v-row>
      <v-col md="12" class="mt-2" v-for="(i, index) in data" :key="index">
        <v-alert class="m-0" dense type="info" v-if="i.end_date >= today">
          {{ i.title }} from <strong>{{ i.start_date }}</strong> to
          <strong>{{ i.end_date }}</strong>
          <nuxt-link style="color: white" to="/setting">
            click more details
          </nuxt-link>
        </v-alert>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" sm="12" md="12" class="mt-4">
        <v-row>
          <v-col
            cols="12"
            sm="6"
            md="4"
            v-for="(i, index) in items"
            :key="index"
          >
            <v-card :style="`border-left: 5px solid #${i.border_color}`">
              <v-list-item three-line>
                <v-list-item-content>
                  <div class="text-overline mb-4">
                    {{ i.title }}
                  </div>
                  <v-list-item-title class="text-h5 mb-1">
                    {{ i.value }}
                  </v-list-item-title>
                </v-list-item-content>

                <v-list-item-avatar size="60" :class="i.color">
                  <v-icon dark> {{ i.icon }}</v-icon>
                </v-list-item-avatar>
              </v-list-item>
            </v-card>
          </v-col>
        </v-row>
      </v-col>

      <v-col cols="12" sm="12" md="8">
        <v-row>
          <v-col cols="12">
            <v-card>
              <div class="heading display-3 overline text-center">
                <b>Daily Attendance Report</b>
              </div>
              <GChart type="ColumnChart" :data="chartData" />
            </v-card>
          </v-col>

          <v-col cols="12">
            <v-card>
              <div class="heading display-3 overline text-center">
                <b>Monthly Attendance Report</b>
              </div>
              <GChart
                type="BarChart"
                :data="barChartData"
                style="height: 500px; max-width: 1200px"
              />
            </v-card>
          </v-col>
        </v-row>
      </v-col>

      <v-col cols="12" sm="12" md="4">
        <v-row>
          <v-col cols="12">
            <v-card>
              <GChart type="PieChart" :data="pieChartData" />
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>

<script>
export default {
  layout: "employee",

  data() {
    return {
      leave_notifications: "",
      currentMonth: "",
      data: [],
      items: [],
      chartData: [
        ["Date", "Present", "Absence", "Lates"],
        [1, 33, 4, 7],
        [2, 17, 6, 3],
        [3, 41, 9, 1],
        [4, 41, 9, 1],
        [5, 41, 9, 1],
        [6, 41, 9, 1],
        [7, 41, 9, 1],
        [8, 41, 9, 1],
        [9, 41, 9, 1],
        [10, 41, 9, 1],
        [11, 33, 4, 7],
        [12, 17, 6, 3],
        [13, 41, 9, 1],
        [14, 41, 9, 1],
        [15, 41, 9, 1],
        [16, 41, 9, 1],
        [17, 41, 9, 1],
        [18, 41, 9, 1],
        [19, 41, 9, 1],
        [20, 41, 9, 1],
        [21, 33, 4, 7],
        [22, 17, 6, 3],
        [23, 41, 9, 1],
        [24, 41, 9, 1],
        [25, 41, 9, 1],
        [26, 41, 9, 1],
        [27, 41, 9, 1],
        [28, 41, 9, 1],
        [29, 41, 9, 1],
        [30, 41, 9, 1],
      ],
      pieChartData: [
        ["Total Employee", "Present", "Absence", "Lates"],

        ["Total Employee", 33, 4, 7],
        ["Total Employee", 17, 6, 3],
        ["Total Employee", 41, 9, 1],
      ],
      barChartData: [
        ["Month", "Present", "Absence", "Lates"],
        ["Jan", 33, 4, 7],
        ["Feb", 33, 4, 7],
        ["Mar", 17, 6, 3],
        ["Apr", 41, 9, 1],
        ["May", 41, 9, 1],
        ["Jun", 41, 9, 1],
        ["Jul", 41, 9, 1],
        ["Aug", 41, 9, 1],
        ["Sep", 41, 9, 1],
        ["Oct", 41, 9, 1],
        ["Nov", 41, 9, 1],
        ["Dec", 41, 9, 1],
      ],
    };
  },
  created() {
    this.get_announcement();
    this.initialize();
    this.get_leave_notification();

    this.first_login_auth = this.$auth.user.first_login;
  },
  computed: {
    first_login() {
      return this.$store.state.first_login;
    },

    today() {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, "0");
      var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
      var yyyy = today.getFullYear();

      return (today = yyyy + "-" + mm + "-" + dd);
    },

    DaysInCurrentMonth() {
      const date = new Date();
      return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
    },
  },
  methods: {
    can(per) {
      return true;
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    initialize() {
      let options = {
        params: {
          company_id: this.$auth.user.employee.company_id,
          employee_id: this.$auth.user.employee.employee_id,
        },
      };

      this.$axios.get(`employee-count`, options).then(({ data }) => {
        this.items = data;
      });
    },

    get_announcement() {
      let id = this.$auth.user.employee.id;
      this.$axios.get(`announcement/employee/${id}`).then(({ data }) => {
        this.data = data;
      });
    },

    get_leave_notification(url = "leave") {
      let payload = {
        params: {
          company_id: this.$auth.user.employee.company_id,
          employee_id: this.$auth.user.employee.employee_id,
        },
      };

      this.$axios.get(url, payload).then(({ data }) => {
        this.leave_notifications = data.data;
      });
    },
  },
};
</script>
