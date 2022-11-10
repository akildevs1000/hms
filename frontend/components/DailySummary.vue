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

    <v-row class="mt-5" dense>
      <v-col md="12" class="mb-2">
        <v-card elevation="0">
          <DailyLogs :data="data" :headers="headers" />
        </v-card>
      </v-col>
    </v-row>

    <v-row justify="center">
      <v-dialog v-model="log_details" max-width="600px">
        <v-card class="darken-1">
          <v-toolbar class="primary" dense dark flat>
            <span class="text-h5 pa-2">Log Details</span>
          </v-toolbar>
          <v-card-text>
            <div class="pt-5">
              <span v-for="(log, index) in log_list" :key="index">
                {{ log.time }}
                <hr />
              </span>
            </div>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
  </div>
  <NoAccess v-else />
</template>
<script>
import DailyLogs from "./DailyLogs.vue";
export default {
  data: () => ({
    model: null,
    time_table_dialog: false,
    log_details: false,
    overtime: false,
    options: {},
    date: null,
    menu: false,
    loading: false,
    time_menu: false,
    Model: "Reports",
    endpoint: "report",
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
    DateRange: true,
    daily_menu: false,
    daily_date: null,
    dailyDate: false,
    loading: false,
    total: 0,
    headers: [
      { text: "E.ID", align: "left", sortable: false, value: "employee_id" },
      {
        text: "Name"
      },
      { text: "In" },
      {
        text: "D.In",
        align: "left",
        sortable: false,
        value: "device_in"
      }
    ],
    payload: {
      from_date: null,
      to_date: null,
      daily_date: null,
      employee_id: "",
      report_type: "Daily",
      department_id: -1,
      status: "Select All",
      late_early: "Select All"
    },
    log_payload: {
      user_id: null,
      device_id: "OX-8862021010011",
      date: null,
      time: null
    },
    log_list: [],
    snackbar: false,
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    csvData: [],
    shifts: [],
    errors: []
  }),
  custom_options: {},
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    }
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
      deep: true
    }
  },
  created() {
    this.loading = true;
    this.getDataFromApi();
    // this.setMonthlyDateRange();
    this.payload.daily_date = new Date().toJSON().slice(0, 10);
    let dt = new Date();
    let y = dt.getFullYear();
    let m = dt.getMonth() + 1;
    m = m < 10 ? "0" + m : m;
    // this.payload.daily_date = `${y}-${m}-11`;
    this.custom_options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company.id
      }
    };
    this.getDepartments(this.custom_options);
    // this.getScheduledEmployees();
    this.getAttendanceEmployees();
  },
  methods: {
    changeReportType(report_type) {
      if (report_type == "Daily") {
        this.setDailyDate();
      } else if (report_type == "Monthly") {
        this.setMonthlyDateRange();
      }
    },
    setMonthlyDateRange() {
      let dt = new Date();
      let y = dt.getFullYear();
      let m = dt.getMonth() + 1;
      m = m < 10 ? "0" + m : m;
      delete this.payload.daily_date;
      this.payload.from_date = `${y}-${m}-01`;
      this.payload.to_date = `${y}-${m}-${31}`;
    },
    setDailyDate() {
      this.payload.daily_date = new Date().toJSON().slice(0, 10);
      delete this.payload.from_date;
      delete this.payload.to_date;
    },
    store_schedule() {
      let { user_id, date, time, device_id } = this.log_payload;
      let log_payload = {
        UserID: user_id,
        LogTime: date + " " + time + ":00",
        DeviceID: device_id,
        company_id: this.$auth.user.company.id
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
        this.shifts = data.data.map(e => ({
          name: e.name,
          on_duty_time: (e.time_table && e.time_table.on_duty_time) || "",
          off_duty_time: (e.time_table && e.time_table.off_duty_time) || ""
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
          if (this.scheduled_employees.length > 0) {
            this.scheduled_employees.unshift({
              system_user_id: "",
              name_with_user_id: "Select All"
            });
          }
          this.loading = false;
        });
    },
    getScheduledEmployees() {
      this.$axios.get(`/scheduled_employees_with_type`).then(({ data }) => {
        this.scheduled_employees = data;
      });
    },
    getAttendanceEmployees() {
      this.$axios.get(`/attendance_employees`).then(({ data }) => {
        let res = data.map(e => e.employee_attendance);
        this.scheduled_employees = data.map(e => e.employee_attendance);
        this.scheduled_employees.unshift({
          system_user_id: "",
          name_with_user_id: "Select All"
        });
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
        .catch(err => console.log(err));
    },
    caps(str) {
      return str.replace(/_/g, " ").replace(/\b\w/g, c => c.toUpperCase());
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    fetch_logs() {
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint) {
      // if (daily) {
      //   delete this.payload.from_date;
      //   delete this.payload.to_date;
      // }
      // if (!this.payload.report_type) {
      //   alert("Select report type");
      //   return;
      // }
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
          ot: this.overtime ? 1 : 0
        }
      };
      this.$axios.get(url, options).then(({ data }) => {
        this.data = data.data;
        this.csvData = data.data.map(e => ({
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
          "D.Out": (e.device_out && e.device_out.name) || "---"
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
        working_hours: item.shift.working_hours || "---"
      };
      if (item && !item.time_table) {
        return shift;
      }
      let time_table = item.time_table;
      return { ...shift, ...time_table };
    },
    editItem(item) {
      this.log_list = [];
      let options = {
        params: {
          per_page: 500,
          UserID: item.employee_id,
          LogTime: item.edit_date,
          company_id: this.$auth.user.company.id
        }
      };
      this.log_details = true;
      this.$axios.get("attendance_single_list", options).then(({ data }) => {
        this.log_list = data.data;
      });
      // this.editedIndex = this.data.indexOf(item);
      // this.editedItem = Object.assign({}, item);
      // this.dialog = true;
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    pdfDownload() {
      let path = process.env.BACKEND_URL + "/pdf";
      let pdf = document.createElement("a");
      pdf.setAttribute("href", path);
      pdf.setAttribute("target", "_blank");
      pdf.click();
    },
    generateReport(url) {
      let path = process.env.BACKEND_URL + "/" + url;
      let report = document.createElement("a");
      if (this.payload.report_type == "Daily") {
        let status = this.payload.status;
        if (url == "present") {
          status = "P";
        } else if (url == "absent") {
          status = "A";
        } else if (url == "missing") {
          status = "Missing";
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
        let data = this.payload;
        let company_id = this.$auth.user.company.id;
        const { page, itemsPerPage } = this.options;
        report.setAttribute(
          "href",
          process.env.BACKEND_URL +
            `/daily_${url}?page=${page}&per_page=${itemsPerPage}&company_id=${company_id}&status=${status}&daily_date=${data.daily_date}&department_id=${data.department_id}&employee_id=${data.employee_id}`
        );
        report.setAttribute("target", "_blank");
        report.click();
        return;
      }
      report.setAttribute("href", path);
      report.setAttribute("target", "_blank");
      report.click();
    }
  },
  components: { DailyLogs }
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}
/*
tr:nth-child(even) {
  background-color: #dddddd;
} */
</style>
