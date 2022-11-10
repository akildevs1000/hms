<template>
  <div v-if="can(`attendance_single_access`)">
    <v-row justify="center">
      <v-dialog v-model="time_table_dialog" max-width="600px">
        <v-card class="darken-1">
          <v-toolbar class="primary" dense dark flat>
            <span class="text-h5">Time Slots</span>
          </v-toolbar>
          <v-card-text>
            <ol class="pa-3">
              <li v-for="(shift, index) in shifts" :key="index">
                {{ shift.name }} ({{ shift.on_duty_time }} -
                {{ shift.off_duty_time }})
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
    </v-row>
    <v-toolbar class="primary" dark flat>
      <v-btn
        v-if="can(`attendance_single_pdf_access`)"
        small
        class="primary darken-2"
      >
        <v-icon class="mr-1" small>mdi-file-outline</v-icon>
        PDF
      </v-btn>
      &nbsp;
      <v-btn
        v-if="can(`attendance_single_csv_access`)"
        small
        class="primary darken-2"
        @click="export_csv"
      >
        <v-icon class="mr-1" small>mdi-file</v-icon>
        CSV
      </v-btn>
      <v-spacer />
    </v-toolbar>
    <v-data-table
      v-if="can(`attendance_single_view_access`)"
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
    Model: "Attendance",
    endpoint: "attendance_single_list",
    search: "",
    snackbar: false,
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
      { text: "E.ID", align: "left", sortable: false, value: "UserID" },
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
        text: "Shift",
        align: "left",
        sortable: false,
        value: "employee.schedule.shift_type.name",
      },
      {
        text: "Shift",
        align: "left",
        sortable: false,
        value: "employee.schedule.shift.name",
      },
      { text: "Time", align: "left", sortable: false, value: "time" },
      {
        text: "Device",
        align: "left",
        sortable: false,
        value: "device.short_name",
      },
      {
        text: "Location",
        align: "left",
        sortable: false,
        value: "device.location",
      },
    ],
    payload: {
      employee_id: null,
    },
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    shifts: [],
    errors: [],
  }),
  custom_options: {},
  created() {
    this.getDataFromApi();
  },

  methods: {
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

    getDataFromApi(url = this.endpoint) {
      let [id, date] = this.$route.params.id.split("_");
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          page: page,
          UserID: id,
          LogTime: date,
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios.get(url, options).then(({ data }) => {
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
      });
    },
    json_to_csv(json) {
      let data = json.map((e) => ({
        Date: e.date,
        "E.ID": e.UserID,
        "First Name": e.employee.first_name,
        Department:
          (e.employee.department && e.employee.department.name) || "---",
        "Shift Type":
          e.employee.schedule.shift_type && e.employee.schedule.shift_type.name,
        Shift:
          (e.employee.schedule.shift && e.employee.schedule.shift.name) ||
          "---",
        Time: e.time,
        Device: (e.device && e.device.short_name) || "---",
        Location: (e.device && e.device.location) || "---",
      }));

      let header = Object.keys(data[0]).join(",") + "\n";

      let rows = "";

      data.forEach((e) => {
        rows += Object.values(e).join(",").trim() + "\n";
      });

      return header + rows;
    },

    export_csv() {
      if (this.data.length == 0) {
        this.snackbar = true;
        this.response = "No record to download";
        return;
      }

      let csvData = this.json_to_csv(this.data);

      let element = document.createElement("a");
      element.setAttribute(
        "href",
        "data:text/csv;charset=utf-8, " + encodeURIComponent(csvData)
      );
      element.setAttribute("download", "download.csv");
      document.body.appendChild(element);
      element.click();
      document.body.removeChild(element);
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
  },
};
</script>
