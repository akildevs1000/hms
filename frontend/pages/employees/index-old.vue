<template>
  <div v-if="can('employee_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>

    <div v-if="!loading">
      <v-row class="mt-5">
        <v-col cols="6">
          <h3>{{ Model }}</h3>
          <div>Dashboard / {{ Model }}</div>
        </v-col>
        <v-col cols="6">
          <div class="text-left">
            <v-btn
              small
              class="primary--text pt-4 pb-4"
              to="/employees/employee_list"
            >
              <v-icon class="pa-0">mdi-menu</v-icon>
            </v-btn>
            <v-btn x-small class="primary pt-4 pb-4" to="/employees">
              <v-icon class="pa-0">mdi-grid</v-icon>
            </v-btn>
          </div>
          <div class="text-right">
            <v-btn
              v-if="can('employee_import_access')"
              small
              dark
              class="mb-2 primary"
              @click="dialog = true"
            >
              Import <v-icon right dark>mdi-cloud-upload</v-icon>
            </v-btn>

            <v-btn
              v-if="can('employee_export_access')"
              small
              dark
              class="mb-2 primary"
              @click="export_submit"
            >
              Export <v-icon right dark>mdi-cloud-download</v-icon>
            </v-btn>

            <v-dialog v-model="dialog" max-width="500px">
              <v-card>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" class="mb-2">
                        <span class="headline">Import Employee</span>
                      </v-col>
                      <v-col cols="12">
                        <v-file-input
                          accept="text/csv"
                          v-model="files"
                          placeholder="Upload your file"
                          label="File"
                          prepend-icon="mdi-paperclip"
                        >
                          <template v-slot:selection="{ text }">
                            <v-chip v-if="text" small label color="primary">
                              {{ text }}
                            </v-chip>
                          </template>
                        </v-file-input>
                        <br />
                        <a href="/employees.csv" download> Download Sample</a>
                        <br />
                        <span
                          v-if="errors && errors.length > 0"
                          class="error--text"
                          >{{ errors[0] }}</span
                        >
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn class="error" small @click="close"> Cancel </v-btn>

                  <v-btn
                    class="primary"
                    :loading="btnLoader"
                    small
                    @click="save"
                    >Save</v-btn
                  >
                </v-card-actions>
              </v-card>
            </v-dialog>

            <v-btn
              v-if="can('employee_create')"
              @click="createEmployee"
              small
              dark
              class="mb-2 primary"
              >{{ Model }} +
            </v-btn>
          </div>
        </v-col>
      </v-row>
      <v-row>
        <v-col xs="12" sm="12" md="3" cols="12">
          <v-select
            @change="getDataFromApi(`employee`)"
            outlined
            v-model="per_page"
            :items="[50, 100, 500,1000]"
            dense
            placeholder="Per Page Records"
          ></v-select>
        </v-col>

        <v-col xs="12" sm="12" md="3" offset-md="6" cols="12">
          <v-text-field
            outlined
            @input="searchIt"
            v-model="search"
            dense
            placeholder="Search..."
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row v-if="can('employee_view')">
        <v-col
          xs="12"
          sm="12"
          md="3"
          cols="12"
          v-for="(item, index) in data"
          :key="index"
        >
          <v-card style="min-height: 209px">
            <v-card-title>
              <v-spacer></v-spacer>
              <v-icon
                v-if="can(`employee_edit`)"
                @click="editItem(item)"
                color="secondary"
                small
                >mdi-pencil</v-icon
              >
              <v-icon
                v-if="can(`employee_delete`)"
                @click="deleteItem(item)"
                color="red"
                small
                >mdi-delete</v-icon
              >
            </v-card-title>
            <v-card-text class="text-center" @click="res(item.id)">
              <div>
                <v-img
                  style="
                    border-radius: 50%;
                    height: 120px;
                    width: 35%;
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

      <v-row>
        <v-col>
          <div color="pt-2" class="text-center">
            <v-btn
              @click="getDataFromApi(prev_page_url)"
              :disabled="prev_page_url ? false : true"
              color="primary"
              small
              elevation="11"
            >
              <v-icon dark>mdi-chevron-double-left </v-icon>
            </v-btn>
            <v-btn
              @click="getDataFromApi(next_page_url)"
              :disabled="next_page_url ? false : true"
              color="primary"
              small
              elevation="11"
            >
              <v-icon dark>mdi-chevron-double-right </v-icon>
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </div>
    <Preloader v-else />
  </div>

  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    color: "primary",
    files: "",
    dialog: false,
    Model: "Employee",
    endpoint: "employee",
    search: "",
    loading: false,
    data: [],
    errors: [],
    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 10,
    response: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0
  }),
  async created() {
    this.loading = false;
    this.getDataFromApi();
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true
    }
  },
  methods: {
    close() {
      this.dialog = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },

    json_to_csv(json) {
      let data = json.map(e => ({
        first_name: e.first_name,
        last_name: e.last_name,
        user_name: e.user.name,
        email: e.user.email,
        phone_number: e.phone_number,
        whatsapp_number: e.whatsapp_number,
        phone_relative_number: e.phone_relative_number,
        whatsapp_relative_number: e.whatsapp_relative_number,
        employee_id: e.employee_id,
        joining_date: e.show_joining_date,
        department_code: e.department_id,
        designation_code: e.designation_id,
        department: e.department.name,
        designation: e.designation.name
      }));

      let header = Object.keys(data[0]).join(",") + "\n";

      let rows = "";

      data.forEach(e => {
        rows +=
          Object.values(e)
            .join(",")
            .trim() + "\n";
      });

      return header + rows;
    },
    res(id) {
      if (!this.can("employee_single_view_access")) {
        return false;
      }
      this.goDetails(id);
    },

    export_submit() {
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

    save() {
      let payload = new FormData();
      payload.append("employees", this.files);
      payload.append("company_id", this.$auth?.user?.company?.id);

      let options = {
        headers: {
          "Content-Type": "multipart/form-data"
        }
      };

      this.btnLoader = true;
      this.$axios
        .post("/employee/import", payload, options)
        .then(({ data }) => {
          this.btnLoader = false;

          if (!data.status) {
            this.errors = data.errors;
            payload.delete("employees");
          } else {
            this.errors = [];
            this.snackbar = true;

            this.response = "Employees imported successfully";

            this.getDataFromApi();

            this.close();
          }
        })
        .catch(e => {
          if (e.toString().includes("Error: Network Error")) {
            this.errors = [
              "File is modified.Please cancel the current file and try again"
            ];
          }
        });
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    createEmployee() {
      if (this.total >= this.max_employee) {
        alert(`You cannot add more than ${this.max_employee} employees`);
        return;
      }

      this.$router.push(`/employees/create`);
    },
    goDetails(id) {
      this.$router.push(`/employees/details/${id}`);
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
    getDataFromApi(url = this.endpoint) {
      // this.loading = true;
      let options = {
        params: {
          per_page: this.per_page,
          company_id: this.$auth?.user?.company?.id
        }
      };

      this.$axios.get(`${url}`, options).then(({ data }) => {
        this.data = data.data;
        this.total = data.total;
        this.max_employee = this.$auth.user.company.max_employee;
        this.next_page_url = data.next_page_url;
        this.prev_page_url = data.prev_page_url;
        this.current_page = data.current_page;
        this.loading = false;
      });
    },
    editItem(item) {
      this.$router.push(`/employees/${item.id}`);
    },
    deleteItem(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.$axios.delete(this.endpoint + "/" + item.id).then(res => {
          const index = this.data.indexOf(item);
          this.data.splice(index, 1);
          this.getDataFromApi();
        });
    }
  }
};
</script>
