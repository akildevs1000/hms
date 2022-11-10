<template>
  <div v-if="can('salary_access')">
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
          <div class="text-right">
            <v-dialog v-model="dialog" max-width="500px">
              <v-card>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12">
                        <span class="headline">Salary</span>
                      </v-col>
                      <v-col cols="12" class="mb-2">
                        <v-autocomplete
                          label="Select Employee"
                          v-model="editedItem.employee_id"
                          :items="employees"
                          item-text="first_name"
                          item-value="id"
                        >
                        </v-autocomplete>
                        <span
                          v-if="errors && errors.employee_id"
                          class="error--text"
                          >{{ errors.employee_id[0] }}</span
                        >
                        <v-autocomplete
                          label="Select Salary Type"
                          v-model="editedItem.salary_type_id"
                          :items="salary_types"
                          item-text="name"
                          item-value="id"
                        >
                        </v-autocomplete>
                        <span
                          v-if="errors && errors.salary_type_id"
                          class="error--text"
                          >{{ errors.salary_type_id[0] }}</span
                        >

                        <v-text-field
                          v-model="editedItem.amount"
                          label="Amount"
                        ></v-text-field>
                        <span
                          v-if="errors && errors.amount"
                          class="error--text"
                          >{{ errors.amount[0] }}</span
                        >
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

            <v-btn
              v-if="can('salary_create')"
              small
              dark
              class="mb-2 primary"
              @click="dialog = true"
              >{{ Model }} +</v-btn
            >
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
      <v-row v-if="can('salary_view')">
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
                v-if="can(`salary_edit`)"
                @click="editItem(item)"
                color="secondary"
                small
                >mdi-pencil</v-icon
              >

              <v-icon
                v-if="can(`salary_delete`)"
                @click="deleteItem(item)"
                color="red"
                small
                >mdi-delete</v-icon
              >
            </v-card-title>

            <v-card-text class="text-center" @click="goDetails(item.id)">
              <div>
                <v-img
                  style="
                    border-radius: 50%;
                    height: 125px;
                    width: 50%;
                    margin: 0 auto;
                  "
                  :src="item.employee.profile_picture || '/no-image.PNG'"
                >
                </v-img>
              </div>

              <div>
                <b>{{ item.employee.first_name }}</b>
              </div>

              <div>{{ item.amount }} AED</div>
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
    Model: "Salary",
    endpoint: "salary",
    search: "",
    loading: false,
    data: [],
    employees: [],
    salary_types: [],
    editedItem: {
      employee_id: "",
      salary_type_id: "",
      amount: "",
    },
    defaultItem: {
      employee_id: "",
      salary_type_id: "",
      amount: "",
    },

    errors: [],
    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 10,
    response: "",
    snackbar: false,
    editedIndex: -1,
  }),
  async created() {
    this.loading = false;
    this.getDataFromApi();
    this.getEmployees();
    this.getSalaryTypes();
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    getEmployees() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth?.user?.company?.id,
        },
      };

      this.$axios.get(`employee`, options).then(({ data }) => {
        this.employees = data.data;
        this.loading = false;
      });
    },
    getSalaryTypes() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth?.user?.company?.id,
        },
      };

      this.$axios.get(`salary_type`, options).then(({ data }) => {
        this.salary_types = data.data;
        this.loading = false;
      });
    },
    close() {
      this.dialog = false;
      this.errors = [];
      this.editedIndex = -1;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    save() {
      if (this.editedIndex > -1) {
        this.$axios
          .put(`salary/` + this.editedItem.id, this.editedItem)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.editedItem.company_id = this.$auth?.user?.company?.id;

        this.$axios
          .post(`/salary`, this.editedItem)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },
    goDetails(id) {
      this.$router.push(`/salary/details/${id}`);
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
          company_id: this.$auth?.user?.company?.id,
        },
      };

      this.$axios.get(`${url}`, options).then(({ data }) => {
        this.data = data.data;
        this.total = data.data;
        this.next_page_url = data.next_page_url;
        this.prev_page_url = data.prev_page_url;
        this.current_page = data.current_page;
        this.loading = false;
      });
    },
    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      let new_item = {
        id: item.id,
        employee_id: item.employee_id,
        salary_type_id: item.salary_type_id,
        amount: item.amount,
      };
      this.editedItem = new_item;
      this.dialog = true;
    },
    deleteItem(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.$axios.delete(this.endpoint + "/" + item.id).then((res) => {
          const index = this.data.indexOf(item);
          this.data.splice(index, 1);
        });
    },
  },
};
</script>
