<template>
  <div v-if="can(`attendance_access`)">
    <v-row justify="center">
      <v-dialog v-model="time_table_dialog" max-width="600px">
        <v-card class="darken-1">
          <v-toolbar class="primary" dense dark flat>
            <span class="text-h5">Time Slots</span>
          </v-toolbar>
          <v-card-text>
            <ol class="pa-3">
              <li v-for="(shift, index) in shifts" :key="index">
                {{ shift.name }}
                {{
                  shift.on_duty_time
                    ? `(${shift.on_duty_time} - ${shift.off_duty_time})`
                    : ""
                }}
              </li>
            </ol>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
      <v-col cols="12">
        <v-card elevation="2" class="pa-5">
          <v-row class="pt-2" dense>
            <v-col md="12">
              <h5>Filters</h5>
            </v-col>
            <v-col md="5">
              Departments
              <v-autocomplete
                @change="getEmployeesByDepartment"
                class="mt-2"
                outlined
                dense
                v-model="payload.department_id"
                x-small
                :items="departments"
                item-value="id"
                item-text="name"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col md="5">
              Employee ID
              <v-autocomplete
                class="mt-2"
                outlined
                dense
                v-model="payload.employee_id"
                x-small
                :items="scheduled_employees"
                item-value="system_user_id"
                item-text="name_with_user_id"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col md="5">
              <div class="text-left">
                <v-menu
                  ref="from_menu"
                  v-model="from_menu"
                  :close-on-content-click="false"
                  :return-value.sync="from_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <div class="mb-1">From Date</div>
                    <v-text-field
                      :hide-details="payload.from_date"
                      outlined
                      dense
                      v-model="payload.from_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="payload.from_date"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="from_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="$refs.from_menu.save(payload.from_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </div>
            </v-col>
            <v-col md="5">
              <div class="mb-1">To Date</div>
              <div class="text-left">
                <v-menu
                  ref="to_menu"
                  v-model="to_menu"
                  :close-on-content-click="false"
                  :return-value.sync="to_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :hide-details="payload.to_date"
                      outlined
                      dense
                      v-model="payload.to_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="payload.to_date" no-title scrollable>
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="to_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="$refs.to_menu.save(payload.to_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </div>
            </v-col>
            <v-col md="5">
              Status
              <v-select
                class="mt-2"
                outlined
                dense
                v-model="payload.status"
                x-small
                :items="[`Select All`, `Present`, `Absent`, `Missing`]"
                item-value="id"
                item-text="name"
                :hide-details="true"
              ></v-select>
            </v-col>
            <v-col md="5">
              Late/Early
              <v-select
                s
                class="mt-2"
                outlined
                dense
                v-model="payload.late_early"
                x-small
                :items="[`Select All`, `Late`, `Early`]"
                item-value="id"
                item-text="name"
                :hide-details="true"
              ></v-select>
            </v-col>
            <v-col md="12">
              <v-checkbox
                dense
                v-model="overtime"
                label="Overtime"
                hide-details
              />
            </v-col>

            <v-col md="12">
              <div class="mb-5">
                <v-btn
                  small
                  :loading="loading"
                  color="primary"
                  @click="fetch_logs"
                >
                  <v-icon small class="pr-1">mdi-history</v-icon>
                  Fetch records
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
    <v-dialog v-model="add_fake_log" width="500">
      <v-card>
        <v-card-title class="text-h5 primary white--text darken-2" dark>
          Generate Log
        </v-card-title>

        <v-card-text class="pa-3">
          <v-row>
            <v-col md="12">
              <v-text-field
                v-model="log_payload.user_id"
                label="User Id"
              ></v-text-field>
              <span v-if="errors && errors.user_id" class="text-danger mt-2">{{
                errors.user_id[0]
              }}</span>
            </v-col>
            <v-col md="12">
              <v-text-field
                v-model="log_payload.device_id"
                label="Device Id"
              ></v-text-field>
              <span
                v-if="errors && errors.device_id"
                class="text-danger mt-2"
                >{{ errors.device_id[0] }}</span
              >
            </v-col>
            <v-col md="12">
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
                    v-model="log_payload.date"
                    label="Date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="log_payload.date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.menu.save(log_payload.date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>
            <v-col md="12">
              <v-menu
                ref="time_menu_ref"
                v-model="time_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="log_payload.time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="log_payload.time"
                    label="Time In"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="time_menu"
                  v-model="log_payload.time"
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
                    @click="$refs.time_menu_ref.save(log_payload.time)"
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
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            small
            :loading="loading"
            color="primary"
            @click="store_schedule"
          >
            Submit
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-toolbar class="primary" dark flat>
      <v-btn small class="primary darken-2" @click="get_time_slots">
        <v-icon class="mr-1">mdi-clock-outline</v-icon>
        Time Slots
      </v-btn>
      &nbsp;
      <v-btn
        v-if="can(`attendance_summary_access`)"
        small
        class="primary darken-2"
        to="/summary"
      >
        <v-icon class="mr-1">mdi-chart-bar</v-icon>
        Summary
      </v-btn>
      <v-spacer />
      &nbsp;

      <v-btn
        v-if="can(`attendance_pdf_access`)"
        small
        class="primary darken-2"
        @click="pdfDownload"
      >
        <v-icon class="mr-1" small>mdi-file-outline</v-icon>
        PDF
      </v-btn>
      &nbsp;
      <CSV
        v-if="can(`attendance_csv_access`)"
        :data="csvData"
        :headers="headers"
      />
      &nbsp;
      <v-btn
        v-if="can(`attendance_log_access`)"
        small
        class="primary darken-2"
        @click="add_fake_log = true"
      >
        <v-icon class="mr-1" small>mdi-file-outline</v-icon>
        Log +
      </v-btn>
    </v-toolbar>
    <v-data-table
      v-if="can(`attendance_log_view_access`)"
      :headers="headers"
      :items="data"
      :server-items-length="total"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [50, 100, 500,1000],
      }"
      class="elevation-1"
    >
      <template v-slot:item.employee_id="{ item }">
        <NuxtLink
          :to="`/attendance_report/${item.employee_id}_${item.edit_date}`"
          >{{ item.employee_id
          }}<v-icon small color="black">mdi-open-in-new</v-icon></NuxtLink
        >
      </template>
      <template v-slot:item.status="{ item }">
        <v-icon v-if="item.status == 'A'" color="error">mdi-close</v-icon>

        <v-icon v-else-if="item.status == 'P'" color="success darken-1"
          >mdi-check</v-icon
        >
        <v-icon v-else-if="item.status == 'H'" color="grey darken-1"
          >mdi-check</v-icon
        >
        <span v-else>{{ item.status }}</span>
      </template>

      <template v-slot:item.shift="{ item }">
        <v-tooltip v-if="item && item.shift" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <div class="primary--text" v-bind="attrs" v-on="on">
              {{ item.shift.name }}
            </div>
          </template>

          <div
            v-for="(iterable, index) in getDataForToolTip(item)"
            :key="index"
          >
            <span v-if="index !== 'id'">
              {{ caps(index) }}: {{ iterable || "---" }}</span
            >
          </div>
        </v-tooltip>
        <span v-else>---</span>
      </template>

      <template v-slot:item.device_in="{ item }">
        <v-tooltip v-if="item && item.device_in" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <div class="primary--text" v-bind="attrs" v-on="on">
              {{ (item.device_in && item.device_in.short_name) || "---" }}
            </div>
          </template>
          <div v-for="(iterable, index) in item.device_in" :key="index">
            <span v-if="index !== 'id'">
              {{ caps(index) }}: {{ iterable || "---" }}</span
            >
          </div>
        </v-tooltip>
        <span v-else>---</span>
      </template>

      <template v-slot:item.device_out="{ item }">
        <v-tooltip v-if="item && item.device_out" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <div class="primary--text" v-bind="attrs" v-on="on">
              {{ (item.device_out && item.device_out.short_name) || "---" }}
            </div>
          </template>
          <div v-for="(iterable, index) in item.device_out" :key="index">
            <span v-if="index !== 'id'">
              {{ caps(index) }}: {{ iterable || "---" }}</span
            >
          </div>
        </v-tooltip>
        <span v-else>---</span>
      </template>
    </v-data-table>
    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    time_table_dialog: false,
    overtime: false,
    options: {},
    date: null,
    menu: false,
    loading: false,
    time_menu: false,
    Model: "Daily",
    endpoint: "auto_report",
    search: "",
    snackbar: false,
    add_fake_log: false,
    dialog: false,
    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,
    ids: [],
    departments: [],
    scheduled_employees: [],

    loading: false,
    total: 0,
    headers: [
      { text: "Date", align: "left", sortable: false, value: "date" },
      { text: "E.ID", align: "left", sortable: false, value: "employee_id" },
      {
        text: "First Name",
        align: "left",
        sortable: false,
        value: "employee.first_name",
      },
      {
        text: "Dept",
        align: "left",
        sortable: false,
        value: "employee.department.name",
      },
      {
        text: "Shift Type",
        align: "left",
        sortable: false,
        value: "shift_type.name",
      },
      {
        text: "Shift",
        align: "left",
        sortable: false,
        value: "shift",
      },
      { text: "Status", align: "left", sortable: false, value: "status" },
      { text: "In", align: "left", sortable: false, value: "in" },
      { text: "Out", align: "left", sortable: false, value: "out" },
      {
        text: "Total Hrs",
        align: "left",
        sortable: false,
        value: "total_hrs",
      },
      { text: "OT", align: "left", sortable: false, value: "ot" },
      {
        text: "Late coming",
        align: "left",
        sortable: false,
        value: "late_coming",
      },
      {
        text: "Early Going",
        align: "left",
        sortable: false,
        value: "early_going",
      },
      {
        text: "D.In",
        align: "left",
        sortable: false,
        value: "device_in",
      },
      {
        text: "D.Out",
        align: "left",
        sortable: false,
        value: "device_out",
      },
    ],
    payload: {
      from_date: null,
      to_date: null,
      employee_id: null,
      department_id: -1,
      status: "Select All",
      late_early: "Select All",
    },
    log_payload: {
      user_id: null,
      device_id: "OX-8862021010011",
      date: null,
      time: null,
    },

    snackbar: false,
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    csvData: [],
    shifts: [],
    errors: [],
  }),
  custom_options: {},

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    this.loading = true;

    let dt = new Date();
    let y = dt.getFullYear();
    let m = dt.getMonth() + 1;
    m = m < 10 ? "0" + m : m;
    this.payload.from_date = `${y}-${m}-01`;
    this.payload.to_date = `${y}-${m}-${31}`;

    this.custom_options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company.id,
      },
    };
    this.getDepartments(this.custom_options);
  },

  methods: {
    store_schedule() {
      let { user_id, date, time, device_id } = this.log_payload;
      let log_payload = {
        UserID: user_id,
        LogTime: date + " " + time + ":00",
        DeviceID: device_id,
        company_id: this.$auth.user.company.id,
      };
      this.loading = true;

      this.$axios
        .post(`/generate_log`, log_payload)
        .then(({ data }) => {
          this.fetch_logs();
          this.add_fake_log = false;
          this.loading = false;
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    setEmployeeId(id) {
      this.$store.commit("employee_id", id);
    },
    get_time_slots() {
      this.getShift(this.custom_options);
    },
    getShift(options) {
      this.$axios.get(`/shift`, options).then(({ data }) => {
        this.shifts = data.data.map((e) => ({
          name: e.name,
          on_duty_time: (e.time_table && e.time_table.on_duty_time) || "",
          off_duty_time: (e.time_table && e.time_table.off_duty_time) || "",
        }));
        this.time_table_dialog = true;
      });
    },

    getEmployeesByDepartment() {
      this.$axios
        .get(
          `/employees_by_departments/${this.payload.department_id}`,
          this.custom_options
        )
        .then(({ data }) => {
          this.scheduled_employees = data;
          this.loading = false;
        });
    },

    getScheduledEmployees(options) {
      this.$axios
        .get(`/scheduled_employees_with_type`, options)
        .then(({ data }) => {
          this.scheduled_employees = data;
        });
    },

    getDevices(options) {
      this.$axios.get(`/device`, options).then(({ data }) => {
        this.devices = data.data;
      });
    },

    getDepartments(options) {
      this.$axios
        .get("departments", options)
        .then(({ data }) => {
          this.departments = [{ id: -1, name: "Select All" }].concat(data.data);
        })
        .catch((err) => console.log(err));
    },
    caps(str) {
      return str.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    fetch_logs() {
      this.getDataFromApi();
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      let status = this.payload.status;
      let late_early = this.payload.late_early;

      switch (late_early) {
        case "Select All":
          late_early = "SA";
          break;

        default:
          late_early = late_early.charAt(0);
          break;
      }

      switch (status) {
        case "Select All":
          status = "SA";
          break;

        case "Missing":
          status = "---";
          break;

        default:
          status = status.charAt(0);
          break;
      }

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company.id,
          ...this.payload,
          status,
          late_early,
          ot: this.overtime ? 1 : 0,
        },
      };

      this.$axios.get(url, options).then(({ data }) => {
        this.data = data.data;
        this.csvData = data.data.map((e) => ({
          Date: e.date,
          "E.ID": e.employee_id,
          "First Name": e.employee.first_name,
          Department:
            (e.employee.department && e.employee.department.name) || "---",
          "Shift Type": e.shift_type && e.shift_type.name,
          Shift: (e.shift && e.shift.name) || "---",
          Status: e.status,
          In: e.in,
          Out: e.out,
          "T.Hrs": e.total_hrs,
          OT: e.ot,
          "Late Coming": e.late_coming,
          "Early Going": e.early_going,
          "D.In": (e.device_in && e.device_in.name) || "---",
          "D.Out": (e.device_out && e.device_out.name) || "---",
        }));
        this.total = data.total;
        this.loading = false;
      });
    },

    getDataForToolTip(item) {
      if (item && !item.shift) {
        return {};
      }

      let shift = {
        name: item.shift.name,
        days: item.shift.days,
        ot_interval: item.shift.overtime,
        working_hours: item.shift.working_hours || "---",
      };

      if (item && !item.time_table) {
        return shift;
      }

      let time_table = item.time_table;

      return { ...shift, ...time_table };
    },
    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    pdfDownload() {
      let pdf = document.createElement("a");
      pdf.setAttribute("href", "http://192.168.2.174:8000/api/pdf");
      pdf.setAttribute("target", "_blank");
      pdf.click();
    },
  },
};
</script>
