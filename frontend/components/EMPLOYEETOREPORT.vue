<template>
  <div v-if="can(`department_access`)">
    <div v-if="!loading">
      <v-dialog v-model="dialog" max-width="800px" persistent>
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }} {{ Model }}</span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
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
      <v-row>
        <v-col md="12" cols="12">
          <div class="text-right">
            <v-icon
              v-if="can('employee_edit')"
              @click="addItem()"
              small
              class="grey"
              style="border-radius: 50%; padding: 5px"
              color="secondary"
              >mdi-plus</v-icon
            >
          </div>
        </v-col>
      </v-row>
      <v-row v-if="can('employee_view')">
        <v-col
          xs="12"
          sm="12"
          md="2"
          cols="12"
          v-for="(item, index) in reporters"
          :key="index"
        >
          <v-card style="min-height: 209px">
            <v-card-title>
              <v-spacer></v-spacer>
              <v-icon
                v-if="can(`employee_delete`)"
                @click="deleteItem(item)"
                color="red"
                small
                >mdi-delete</v-icon
              >
            </v-card-title>
            <v-card-text class="text-center">
              <div>
                <v-img
                  style="
                    border-radius: 50%;
                    height: 125px;
                    width: 50%;
                    margin: 0 auto;
                  "
                  :src="item.profile_picture || '/no-profile-image.jpg'"
                >
                </v-img>
              </div>

              <div>
                <b>{{ item.first_name }}</b>
              </div>

              <div>
                {{ (item.designation && item.designation.name) || "" }}
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
    <Preloader v-else style="margin: 0 0 0 750px" />
  </div>
</template>
<script>
export default {
  props: ["currentUser"],
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

    editedIndex: -1,
    editedItem: {
      employee_id: "",
      to_report: ""
    },

    reporters: "",
    reporters_count: "",
    singleEmployee: {},
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

  created() {
    this.loading = true;
    let options = {
      params: {
        per_page: this.options.itemsPerPage,
        company_id: this.$auth.user.company.id
      }
    };

    this.getEmployeeDetails();
    this.getEmployees();
    this.getDataFromApi();
    this.changeEmployeeType();
    this.getReporters();
  },

  methods: {
    caps(str) {
      return str.replace(/\b\w/g, c => c.toUpperCase());
    },

    getEmployeeDetails() {
      this.$axios.get(`employee/${this.currentUser}`).then(({ data }) => {
        this.singleEmployee = data;
      });
    },

    getReporters() {
      let id = this.currentUser;
      this.$axios.get(`reporter_by_employee/${id}`).then(({ data }) => {
        this.reporters = data;
        this.reporters_count = data.length;
        return;
      });
    },

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
      this.loading = true;
      let id = this.currentUser;
      this.$axios.get(`employee/${id}`).then(({ data }) => {
        this.employee_grade = data.grade;
        this.employees_list = this.all_employees.filter(e =>
          e.id != this.currentUser ? e : null
        );
      });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
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
      let payload = {
        params: {
          report_id: item.id
        }
      };
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`employee_remove_reporters` + "/" + this.currentUser, payload)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
            this.getReloadMethods();
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
      // if (this.editedItem.to_report.length < 2) {
      // }

      let payload = {
        report_id: this.editedItem.to_report
      };
      let id = this.currentUser;

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
            this.getReloadMethods();
          }
        })
        .catch(res => console.log(res));
    },
    getReloadMethods() {
      this.getEmployees();
      this.getDataFromApi();
      this.changeEmployeeType();
      this.getReporters();
    },

    addItem() {
      if (this.isEmployeeGradeA()) {
        return false;
      }
      if (this.singleEmployee.grade == "C" && this.reporters_count >= 2) {
        alert("cannot assign more then two");
        return;
      }
      if (this.singleEmployee.grade == "B" && this.reporters_count >= 1) {
        alert("cannot assign more then one");
        return;
      }

      this.dialog = true;
    },
    isEmployeeGradeA() {
      if (this.singleEmployee && this.singleEmployee.grade == "A") {
        alert(
          "You cannot assign reporter " +
            this.singleEmployee.first_name +
            " employee grade A "
        );
        return true;
      }
    }
  }
};
</script>
