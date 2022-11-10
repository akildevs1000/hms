<template>
  <div v-if="can(`employee_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6" sm="6" md="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>
    <v-row>
      <v-col xs="12" sm="12" lg="3" cols="12">
        <v-select
          @change="getDataFromApi(`employee`)"
          v-model="pagination.per_page"
          :items="[50, 100, 500, 1000]"
          placeholder="Per Page Records"
          solo
          flat
        ></v-select>
      </v-col>
      <v-col xs="12" sm="12" lg="3" cols="12">
        <v-select
          @change="getDataFromApi(`employee`)"
          v-model="department_id"
          item-text="name"
          item-value="id"
          :items="departments"
          placeholder="Department"
          solo
          flat
        ></v-select>
      </v-col>
      <v-col xs="12" sm="12" lg="3" cols="12">
        <v-text-field
          class=""
          placeholder="Search..."
          solo
          flat
          @input="searchIt"
          v-model="search"
        ></v-text-field>
      </v-col>
    </v-row>

    <div v-if="can(`employee_view`)">
      <v-row>
        <v-col lg="6">
          <v-card class="mb-5" elevation="0">
            <v-toolbar color="background" dark flat>
              <v-app-bar-nav-icon></v-app-bar-nav-icon>
              <v-toolbar-title>Salary Details</v-toolbar-title>
            </v-toolbar>
            <table class="employee-table">
              <tr>
                <th v-for="(item, index) in headers" :key="index">
                  {{ item.text }}
                </th>
              </tr>
              <v-progress-linear
                v-if="loading"
                :active="loading"
                :indeterminate="loading"
                absolute
                color="primary"
              ></v-progress-linear>
              <tr v-for="(item, index) in data" :key="index">
                <td class="text-center">
                  <b>{{ ++index }}</b>
                </td>
                <td>{{ item.system_user_id || "---" }}</td>
                <td>{{ item.display_name || "---" }}</td>
                <td>{{ caps(item && item.designation.name) }}</td>
                <td>{{ caps(item.department.name) }}</td>
                <td>
                  <v-menu bottom left>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn dark-2 icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list width="120" dense>
                      <v-list-item @click="editItem(item)">
                        <v-list-item-title style="cursor:pointer">
                          <v-icon color="secondary" small>
                            mdi-pencil
                          </v-icon>
                          Edit
                        </v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="res(item.id)">
                        <v-list-item-title style="cursor:pointer">
                          <v-icon color="primary" small>
                            mdi-eye
                          </v-icon>
                          Select
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
            </table>
          </v-card>
          <v-row>
            <v-col lg="12" class="float-right">
              <div class="float-right">
                <v-pagination
                  v-model="pagination.current"
                  :length="pagination.total"
                  @input="onPageChange"
                  :total-visible="5"
                ></v-pagination>
              </div>
            </v-col>
          </v-row>
        </v-col>
        <v-col lg="6">
          <v-card>
            <v-toolbar color="background" dark flat>
              <v-app-bar-nav-icon></v-app-bar-nav-icon>
              <v-toolbar-title>Payroll</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon>lgi-magnify</v-icon>
              </v-btn>
              <template v-slot:extension>
                <v-tabs v-model="tab" align-with-title>
                  <v-tabs-slider color="yellow"></v-tabs-slider>
                  <v-tab>
                    Employee
                  </v-tab>
                  <v-tab>
                    Bank Details
                  </v-tab>
                  <v-tab>
                    Salary
                  </v-tab>
                  <v-tab>
                    Allowance
                  </v-tab>
                  <v-tab>
                    Deduction
                  </v-tab>
                </v-tabs>
              </template>
            </v-toolbar>
            <v-tabs-items v-model="tab">
              <v-tab-item>
                <v-card flat>
                  <v-expand-x-transition>
                    <section class="pa-5">
                      <!-- <table>
                        <tr>
                          <th>Role</th>
                          <td>
                            {{ (work && work.role && work.role.name) || "---" }}
                          </td>
                        </tr>
                        <tr>
                          <th>EID</th>
                          <td>
                            {{ (work && work.employee_id) || "----" }}
                          </td>
                        </tr>
                        <tr>
                          <th>Name</th>
                          <td>
                            {{ caps(work && work.first_name) || "---" }}
                          </td>
                        </tr>
                        <tr>
                          <th>Department</th>
                          <td>
                            {{ (work && work.department.name) || "----" }}
                          </td>
                        </tr>

                        <tr>
                          <th>Sub Department</th>
                          <td>
                            {{
                              (work &&
                                work.sub_department &&
                                work.sub_department.name) ||
                                "----"
                            }}
                          </td>
                        </tr>

                        <tr>
                          <th>Email</th>
                          <td>
                            {{ (work && work.user.email) || "----" }}
                          </td>
                        </tr>
                        <tr>
                          <th>Whatsapp Number</th>
                          <td>
                            {{ (work && work.whatsapp_number) || "----" }}
                          </td>
                        </tr>
                        <tr>
                          <th>Joining Date</th>
                          <td>
                            {{ (work && work.joining_date) || "----" }}
                          </td>
                        </tr>
                      </table> -->
                      <v-row class="mb-6" no-guttedrs>
                        <!-- row 1 -->
                        <v-col lg="1" cols="6" sm="6" md="6" class="pr-0 mr-0">
                          <b>Role :</b>
                        </v-col>
                        <v-col lg="3" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ caps(work.role && work.role.name) || "---" }}
                        </v-col>
                        <v-col lg="1" cols="6" sm="6" md="6" class="pr-0 mr-0">
                          <b>EID :</b>
                        </v-col>
                        <v-col lg="3" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ caps(work && work.employee_id) || "---" }}
                        </v-col>
                        <v-col lg="1" cols="6" sm="6" md="6" class="pr-0 mr-0"
                          ><b>Name :</b>
                        </v-col>
                        <v-col lg="3" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ caps(work && work.display_name) || "---" }}
                        </v-col>
                        <!-- row 2 -->
                        <v-col lg="1" cols="6" sm="6" md="6" class="pr-0 mr-0">
                          <b>Dept :</b>
                        </v-col>
                        <v-col lg="3" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{
                            (work &&
                              work.sub_department &&
                              work.sub_department.name) ||
                              "IT"
                          }}
                        </v-col>
                        <v-col lg="1" cols="6" sm="6" md="6" class="pr-0 mr-00">
                          <b> Design: </b>
                        </v-col>
                        <v-col lg="3" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ work && work.designation.name }}
                        </v-col>
                        <v-col lg="1" cols="6" sm="6" md="6" class="px-0 mx-0"
                          ><b>JoinDate:</b>
                        </v-col>
                        <v-col lg="3" cols="6" sm="6" md="6" class="float-left">
                          {{ (work && work.joining_date) || "----" }}
                        </v-col>
                        <v-col lg="2" cols="6" sm="6" md="6" class="px-0 ml-3"
                          ><b>Email :</b>
                        </v-col>
                        <v-col lg="3" cols="6" sm="6" md="6" class="float-left">
                          {{ (work && work.user.email) || "----" }}
                        </v-col>
                        <!-- row 3 -->
                      </v-row>
                    </section>
                  </v-expand-x-transition>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <!-- Bank -->
                <v-card flat>
                  <v-expand-x-transition>
                    <section class="pa-5">
                      <v-row class="mb-6" no-guttedrs>
                        <!-- row 1 -->
                        <v-col lg="2" cols="6" sm="6" md="6" class="pr-0 mr-0">
                          <b>Name :</b>
                        </v-col>
                        <v-col lg="4" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ caps(BankInfo.bank_name) }}
                        </v-col>
                        <v-col lg="2" cols="6" sm="6" md="6" class="pr-0 mr-0">
                          <b>Account No :</b>
                        </v-col>
                        <v-col lg="4" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ caps(BankInfo.account_no) }}
                        </v-col>

                        <v-col lg="2" cols="6" sm="6" md="6" class="pr-0 mr-0"
                          ><b>Title :</b>
                        </v-col>
                        <v-col lg="4" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ caps(BankInfo.account_title) }}
                        </v-col>
                        <!-- row 2 -->
                        <v-col lg="2" cols="6" sm="6" md="6" class="pr-0 mr-0">
                          <b>Iban :</b>
                        </v-col>
                        <v-col lg="4" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ caps(BankInfo.iban) }}
                        </v-col>
                        <v-col lg="2" cols="6" sm="6" md="6" class="pr-0 mr-00">
                          <b>Address : </b>
                        </v-col>
                        <v-col lg="4" cols="6" sm="6" md="6" class="pl-0 ml-0">
                          {{ caps(BankInfo.address) }}
                        </v-col>
                        <!-- row 3 -->
                      </v-row>
                    </section>
                  </v-expand-x-transition>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <!-- Salary -->
                <v-card flat>
                  <v-expand-x-transition>
                    <section class="pa-5">
                      <v-row dense>
                        <v-col md="6" sm="12" cols="12" dense>
                          <label class="col-form-label"
                            >Payment Method
                            <span class="text-danger">*</span></label
                          >
                          <v-select
                            v-model="salary.payment_method"
                            :items="paymentMethod"
                            :hide-details="!errors.payment_method"
                            :error="errors.payment_method"
                            :error-messages="
                              errors && errors.payment_method
                                ? errors.title[0]
                                : ''
                            "
                            dense
                            outlined
                          ></v-select>
                        </v-col>
                        <v-col md="6" cols="12" sm="12" dense>
                          <label class="col-form-label"
                            >Basic Salary<span class="text-danger"
                              >*</span
                            ></label
                          >
                          <v-text-field
                            dense
                            outlined
                            type="number"
                            v-model="salary.basic_salary"
                            :hide-details="!errors.basic_salary"
                            :error="errors.basic_salary"
                            :error-messages="
                              errors && errors.basic_salary
                                ? errors.basic_salary[0]
                                : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="6" cols="12" sm="12" dense>
                          <v-btn color="primary" :loading="loading">Save</v-btn>
                        </v-col>
                      </v-row>
                    </section>
                  </v-expand-x-transition>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <!-- Allowance -->
                <v-card flat>
                  <v-expand-x-transition>
                    <section class="pa-5">
                      <v-row dense>
                        <v-col md="6" sm="12" cols="12" dense>
                          <label class="col-form-label"
                            >Allowance Type
                            <span class="text-danger">*</span></label
                          >
                          <v-select
                            v-model="allowance.name"
                            :items="Allowance"
                            :hide-details="!errors.name"
                            :error="errors.name"
                            :error-messages="
                              errors && errors.name ? errors.title[0] : ''
                            "
                            dense
                            outlined
                          ></v-select>
                        </v-col>
                        <v-col md="6" cols="12" sm="12" dense>
                          <label class="col-form-label"
                            >Amount<span class="text-danger">*</span></label
                          >
                          <v-text-field
                            dense
                            outlined
                            type="number"
                            v-model="allowance.amount"
                            :hide-details="!errors.amount"
                            :error="errors.amount"
                            :error-messages="
                              errors && errors.amount ? errors.amount[0] : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="12" cols="12" sm="12" dense>
                          <label class="col-form-label"
                            >Remark<span class="text-danger">*</span></label
                          >
                          <v-textarea
                            v-model="allowance.remark"
                            dense
                            outlined
                            type="text"
                            :hide-details="!errors.remark"
                            class="input-group--focused"
                          ></v-textarea>
                        </v-col>
                        <v-col md="6" cols="12" sm="12" dense>
                          <v-btn color="primary" :loading="loading">Save</v-btn>
                        </v-col>
                      </v-row>
                    </section>
                  </v-expand-x-transition>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <!-- Allowance -->
                <v-card flat>
                  <v-expand-x-transition>
                    <section class="pa-5">
                      <v-row dense>
                        <v-col md="6" sm="12" cols="12" dense>
                          <label class="col-form-label"
                            >Allowance Type
                            <span class="text-danger">*</span></label
                          >
                          <v-select
                            v-model="allowance.name"
                            :items="Allowance"
                            :hide-details="!errors.name"
                            :error="errors.name"
                            :error-messages="
                              errors && errors.name ? errors.title[0] : ''
                            "
                            dense
                            outlined
                          ></v-select>
                        </v-col>
                        <v-col md="6" cols="12" sm="12" dense>
                          <label class="col-form-label"
                            >Amount<span class="text-danger">*</span></label
                          >
                          <v-text-field
                            dense
                            outlined
                            type="number"
                            v-model="allowance.amount"
                            :hide-details="!errors.amount"
                            :error="errors.amount"
                            :error-messages="
                              errors && errors.amount ? errors.amount[0] : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="12" cols="12" sm="12" dense>
                          <label class="col-form-label"
                            >Remark<span class="text-danger">*</span></label
                          >
                          <v-textarea
                            v-model="allowance.remark"
                            dense
                            outlined
                            type="text"
                            :hide-details="!errors.remark"
                            class="input-group--focused"
                          ></v-textarea>
                        </v-col>
                        <v-col md="6" cols="12" sm="12" dense>
                          <v-btn color="primary" :loading="loading">Save</v-btn>
                        </v-col>
                      </v-row>
                    </section>
                  </v-expand-x-transition>
                </v-card>
              </v-tab-item>
            </v-tabs-items>
          </v-card>
        </v-col>
      </v-row>
      <div></div>
    </div>
    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    tab: null,
    pagination: {
      current: 1,
      total: 0,
      per_page: 10
    },
    options: {},
    Model: "Payroll",
    endpoint: "employee",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    paymentMethod: ["Bank Transfer", "Cash", "Cheque"],
    Allowance: [
      "Transport",
      "Travel",
      "Entertainment",
      "Housing",
      "Uniform",
      "Uniform",
      "Medical/health"
    ],
    headers: [
      {
        text: "#"
      },
      {
        text: "EID"
      },

      {
        text: "Name"
      },
      {
        text: "Designation"
      },
      {
        text: "Department"
      },
      { text: "Actions", align: "center", value: "action", sortable: false }
    ],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
    departments: [],
    department_id: "",
    work: {
      display_name: "",
      first_name: "",
      last_name: "",
      department: "",
      sub_department: "",
      designation: "",
      role: "",
      employee_id: "",
      system_user_id: "",
      user: "",
      profile_picture: "",
      phone_number: "",
      whatsapp_number: "",
      joining_date: ""
    },
    BankInfo: {
      bank_name: "",
      account_no: "",
      account_title: "",
      iban: "",
      address: "",
      remark: "",
      company_id: "",
      employee_id: ""
    },
    salary: {
      basic_salary: "",
      payment_method: "",
      remark: ""
    },
    allowance: {
      name: "",
      amount: "",
      remark: ""
    }
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
    department_id() {
      this.pagination.current = 1;
      this.getDataFromApi();
    }
  },
  created() {
    this.loading = true;
    this.getDepartments();
  },
  mounted() {
    this.getDataFromApi();
  },

  methods: {
    onPageChange() {
      this.getDataFromApi();
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },
    res(id) {
      this.$axios.get(`employee/${id}`).then(({ data }) => {
        this.work = { ...data };
        this.getBankInfo(data.employee_id);
      });
    },
    getBankInfo(id) {
      this.$axios.get(`bankinfo/${id}`).then(({ data }) => {
        this.BankInfo = {
          ...data
        };
      });
    },

    getDepartments() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
        this.departments.unshift({ name: "All", id: "" });
      });
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let department_id = this.department_id;
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          department_id: department_id
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.work = this.data[0];
        this.BankInfo = this.data[0];
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
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
      this.$router.push(`/employees/${item.id}`);
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
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      let salary = {
        name: this.editedItem.name.toLowerCase(),
        company_id: this.$auth.user.company.id
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, salary)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                item => item.id == this.editedItem.id
              );
              this.data.splice(index, 1, {
                id: this.editedItem.id,
                name: this.editedItem.name
              });
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch(err => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, salary)
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
          .catch(res => console.log(res));
      }
    }
  }
};
</script>

<style scoped>
table.employee-table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  /* border: 1px solid #dddddd; */
  text-align: left;
  padding: 8px;
}

table.employee-table tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
