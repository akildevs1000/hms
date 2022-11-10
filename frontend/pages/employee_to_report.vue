<template>
  <div v-if="can(`department_access`)">
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
            v-if="can(`department_delete`)"
            small
            color="error"
            class="mr-2 mb-2"
            @click="delteteSelectedRecords"
            >Delete Selected Records</v-btn
          >

          <v-btn
            v-if="can(`department_create`)"
            small
            color="primary"
            @click="dialog = true"
            class="mb-2"
            >{{ Model }} +</v-btn
          >
        </div>
      </v-col>
    </v-row>

    <v-dialog v-model="dialog" max-width="800px" persistent>
      <v-card>
        <v-card-title>
          <span class="headline">{{ formTitle }} {{ Model }}</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-autocomplete
                  label="Select Employee"
                  v-model="editedItem.employee_id"
                  :items="employees"
                  item-text="first_name"
                  item-value="id"
                  @change="changeEmployeeType"
                >
                </v-autocomplete>
                <span v-if="errors && errors.employee_id" class="error--text">{{
                  errors.employee_id[0]
                }}</span>
              </v-col>

              <v-col cols="12" v-if="employee_grade">
                <v-autocomplete
                  label="Report"
                  v-model="editedItem.to_report"
                  :items="employees_list"
                  item-text="first_name"
                  item-value="id"
                  :multiple="
                    employee_grade && employee_grade == 'C' ? true : false
                  "
                >
                </v-autocomplete>
                <span v-if="errors && errors.to_report" class="error--text">{{
                  errors.to_report[0]
                }}</span>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="error" small @click="close"> Cancel </v-btn>
          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- {{ data }} -->
    <v-data-table
      v-if="can(`employee_report`)"
      v-model="ids"
      show-select
      item-key="id"
      :headers="headers"
      :items="data"
      :server-items-length="total"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [50, 100, 500, 1000]
      }"
      class="elevation-1"
    >
      <!-- <template v-slot:top>
        <v-toolbar flat color="">
          <v-toolbar-title>List</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>

          <v-text-field
            @input="searchIt"
            v-model="search"
            label="Search"
            single-line
            hide-details
          ></v-text-field>
        </v-toolbar>
      </template> -->

      <template v-slot:item.action="{ item }">
        <v-icon
          v-if="can(`department_edit`)"
          color="secondary"
          small
          class="mr-2"
          @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          v-if="can(`department_delete`)"
          color="error"
          small
          @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
      </template>

      <template v-slot:item.report_to="{ item }">
        <span v-for="(rep, index) in item && item.report_to" :key="index">
          <v-chip small class="p-2 mx-1" color="primary">
            {{ rep.first_name }}
          </v-chip>
        </span>
      </template>
    </v-data-table>
    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    Model: "Report Employee",
    options: {},

    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    employees: [],
    all_employees: [],
    employees_list: [],
    employee: [],
    loading: false,
    total: 0,
    employee_grade: "",
    headers: [
      {
        text: "name",
        align: "left",
        sortable: false,
        value: "first_name"
      },
      {
        text: "Reporter",
        align: "left",
        sortable: false,
        value: "report_to"
      },
      { text: "Actions", align: "center", value: "action", sortable: false }
    ],
    editedIndex: -1,
    editedItem: {
      employee_id: "",
      to_report: ""
    },

    response: "",
    data: [],
    errors: []
  }),

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
  mounted() {},
  created() {
    this.loading = true;
    let options = {
      params: {
        per_page: this.options.itemsPerPage,
        company_id: this.$auth.user.company.id
      }
    };

    this.getEmployees();
    this.getDataFromApi();
  },

  methods: {
    getEmployees() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`employee`, options).then(({ data }) => {
        this.all_employees = data.data;
        this.employees = data.data.filter(e =>
          e.report_to.length == 0 ? e : null
        );
      });
    },

    changeEmployeeType() {
      let id = this.editedItem.employee_id;
      this.$axios.get(`employee/${id}`).then(({ data }) => {
        this.employee_grade = data.grade;
        this.employees_list = this.all_employees.filter(e =>
          e.id != this.editedItem.employee_id ? e : null
        );
      });
    },

    getDataFromApi(url = "employee_reporters") {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
      });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    // searchIt(e) {
    //   if (e.length == 0) {
    //     this.getDataFromApi();
    //   } else if (e.length > 2) {
    //     this.getDataFromApi(`${this.endpoint}/search/${e}`);
    //   }
    // },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    delteteSelectedRecords() {
      let just_ids = this.ids.map(e => e.id);
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: just_ids
          })
          .then(res => {
            if (!res.data.status) {
              this.errors = res.data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = res.data.status;
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
          .delete(`employee_remove_reporters` + "/" + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
            this.getEmployees();
            this.getDataFromApi();
          })
          .catch(err => console.log(err));
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      let payload = {
        report_id: this.editedItem.to_report
      };
      let id = this.editedItem.employee_id;

      this.$axios
        .post(`employee_to_reporter/${id}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.data.push(data.record);
            this.snackbar = data.status;
            this.response = data.message;
            this.close();
            this.errors = [];
            this.search = "";
            this.getDataFromApi();
            this.getEmployees();
          }
        })
        .catch(res => console.log(res));
    }

    // saveq() {
    //   let payload = {
    //     name: this.editedItem.name.toLowerCase(),
    //     company_id: this.$auth.user.company.id,
    //     employee_id: this.editedItem.employee_id,
    //   };
    //   if (this.editedIndex > -1) {
    //     this.$axios
    //       .put(this.endpoint + "/" + this.editedItem.id, payload)
    //       .then(({ data }) => {
    //         if (!data.status) {
    //           this.errors = data.errors;
    //         } else {
    //           const index = this.data.findIndex(
    //             (item) => item.id == this.editedItem.id
    //           );
    //           this.data.splice(index, 1, data.record);
    //           this.snackbar = data.status;
    //           this.response = data.message;
    //           this.getDataFromApi();

    //           this.close();
    //         }
    //       })
    //       .catch((err) => console.log(err));
    //   } else {
    //     this.$axios
    //       .post(this.endpoint, payload)
    //       .then(({ data }) => {
    //         if (!data.status) {
    //           this.errors = data.errors;
    //         } else {
    //           this.data.push(data.record);
    //           this.snackbar = data.status;
    //           this.response = data.message;
    //           this.close();
    //           this.errors = [];
    //           this.search = "";
    //           this.getDataFromApi();
    //         }
    //       })
    //       .catch((res) => console.log(res));
    //   }
    // },
  }
};
</script>
