<template>
  <div v-if="can(`shift_access`)">
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
      <v-col cols="6">
        <div class="text-right">
          <v-btn
            v-if="can(`shift_create`)"
            small
            color="primary"
            to="/shift/create1"
            class="mb-2"
            >{{ Model }} +</v-btn
          >
        </div>
      </v-col>
    </v-row>

    <v-card elevation="0" v-if="can(`shift_view`)">
      <v-toolbar class="rounded-md" color="background" dense flat dark>
        <span> {{ Model }} List</span>
      </v-toolbar>
      <table>
        <tr>
          <th v-for="(i, index) in headers" :key="index">{{ i.text }}</th>
        </tr>
        <tr v-for="(item, index) in data" :key="index">
          <td>{{ item && item.name }}</td>
          <td>{{ item && item.shift_type.name }}</td>
          <td>{{ item && item.working_hours }}</td>
          <td>{{ item && item.overtime_interval }}</td>
          <td>{{ item && item.on_duty_time }}</td>
          <td>{{ item && item.off_duty_time }}</td>
          <td>{{ item && item.late_time }}</td>
          <td>{{ item && item.early_time }}</td>
          <td>{{ item && item.beginning_in }}</td>
          <td>{{ item && item.beginning_out }}</td>
          <td>{{ item && item.ending_in }}</td>
          <td>{{ item && item.ending_out }}</td>
          <td>{{ item && item.absent_min_in }}</td>
          <td>{{ item && item.absent_min_out }}</td>
          <td>
            <span v-if="item && !item.days">
              ---
            </span>
            <span v-else v-for="(day, index) in item.days" :key="index">
              {{ day }}
              <span v-if="item && item.days.length - 1 !== index">, </span>
            </span>
          </td>
          <td style="text-align: center">
            <v-icon
              color="secondary"
              small
              class="mr-2"
              @click="editItem(item)"
            >
              mdi-pencil
            </v-icon>
            <v-icon color="error" small @click="deleteItem(item)">
              mdi-delete
            </v-icon>
          </td>
        </tr>
      </table>
    </v-card>

    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    options: {},
    Model: "Shift",
    endpoint: "shift",
    search: "",
    snackbar: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      { text: "Name" },
      { text: "Type" },
      { text: "Working Hrs" },
      { text: "OT Interval" },
      { text: "In" },
      { text: "Out" },
      { text: "Late In" },
      { text: "Early Out" },
      { text: "Start In" },
      { text: "Start Out" },
      { text: "Ending In" },
      { text: "Ending Out" },
      { text: "Absent In" },
      { text: "Absent Out" },
      { text: "Off Days" },
      { text: "Actions" }
    ],
    response: "",
    data: [],
    errors: []
  }),

  watch: {
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
  },

  methods: {
    getDataForToolTip(item) {
      if (item && !item.time_table) {
        return {};
      }

      let time_table = item.time_table;

      return {
        on_duty_time: time_table.on_duty_time || "---",
        off_duty_time: time_table.off_duty_time || "---",
        late_time: time_table.late_time || "---",
        early_time: time_table.early_time || "---",
        beginning_in: time_table.beginning_in || "---",
        ending_in: time_table.ending_in || "---",
        beginning_out: time_table.beginning_out || "---",
        ending_out: time_table.ending_out || "---",
        absent_min_in: time_table.absent_min_in || "---",
        absent_min_out: time_table.absent_min_out || "---"
      };
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

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          page: page,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get(url, options).then(({ data }) => {
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
      });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    editItem(item) {
      this.$router.push(`/shift/${item.id}`);
    },

    delteteSelectedRecords() {
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: this.ids.map(e => e.id)
          })
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
          })
          .catch(err => console.log(err));
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
            }
          })
          .catch(err => console.log(err));
    }
  }
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

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
