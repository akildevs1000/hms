<template>
  <div v-if="can(`designation_access`)">
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
          <!-- <v-btn
            v-if="can(`designation_delete`)"
            small
            color="error"
            class="mr-2 mb-2"
            @click="delteteSelectedRecords"
            >Delete Selected Records</v-btn
          > -->

          <!-- <v-btn
            v-if="can(`designation_create`)"
            small
            color="primary"
            @click="dialog = true"
            class="mb-2"
            >{{ Model }} +</v-btn
          > -->
        </div>
      </v-col>
    </v-row>

    <!-- <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">{{ formTitle }} {{ Model }}</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="editedItem.name"
                  label="Designation"
                ></v-text-field>
                <span v-if="errors && errors.name" class="error--text">{{
                  errors.name[0]
                }}</span>
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                  label="Select Department"
                  v-model="editedItem.department_id"
                  :items="departments"
                  item-text="name"
                  item-value="id"
                >
                </v-autocomplete>
                <span
                  v-if="errors && errors.department_id"
                  class="error--text"
                  >{{ errors.department_id[0] }}</span
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
    </v-dialog> -->

    <v-row>
      <!-- <v-card>
          <v-toolbar color="primary" dense flat dark>
            <v-card-title>
              <span class="headline">{{ formTitle }} {{ Model }}</span>
            </v-card-title>
          </v-toolbar>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.name"
                    label="Designation"
                  ></v-text-field>
                  <span v-if="errors && errors.name" class="error--text">{{
                    errors.name[0]
                  }}</span>
                </v-col>
                <v-col cols="12">
                  <v-autocomplete
                    label="Select Department"
                    v-model="editedItem.department_id"
                    :items="departments"
                    item-text="name"
                    item-value="id"
                  >
                  </v-autocomplete>
                  <span
                    v-if="errors && errors.department_id"
                    class="error--text"
                    >{{ errors.department_id[0] }}</span
                  >
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-btn class="primary ml-4" @click="save">Save</v-btn>
          </v-card-actions>
        </v-card> -->

      <v-col md="4" lg="4">
        <v-card elevation="0">
          <v-toolbar color="background" dense flat dark>
            <span>{{ formTitle }} {{ Model }}</span>
          </v-toolbar>
          <v-divider class="py-0 my-0"></v-divider>
          <v-card-text>
            <v-container>
              <v-row class="mt-2">
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.name"
                    placeholder="Designation"
                    outlined
                    dense
                  ></v-text-field>
                  <span v-if="errors && errors.name" class="error--text">{{
                    errors.name[0]
                  }}</span>
                </v-col>
                <v-col cols="12">
                  <v-autocomplete
                    v-model="editedItem.department_id"
                    :items="departments"
                    item-text="name"
                    item-value="id"
                    placeholder="Select Departments"
                    outlined
                    dense
                  >
                  </v-autocomplete>
                  <span
                    v-if="errors && errors.department_id"
                    class="error--text"
                    >{{ errors.department_id[0] }}</span
                  >
                </v-col>
                <v-card-actions>
                  <v-btn class="error" @click="close"> Cancel </v-btn>
                  <v-btn class="primary" @click="save">Save</v-btn>
                </v-card-actions>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col md="8">
        <!-- <v-row>
          <v-col xs="12" sm="12" md="4" cols="12">
            <v-text-field
              class="form-control py-1 mb-3 custom-text-box floating shadow-none"
              placeholder="Search..."
              solo
              flat
              @input="searchIt"
              v-model="search"
              :hide-details="true"
            ></v-text-field>
          </v-col>
        </v-row> -->

        <v-card class="mb-5 " elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span> {{ Model }} List</span>
          </v-toolbar>

          <v-text-field
            class="form-control py-0  ma-1 mb-0 w-25 float-start custom-text-box floating shadow-none"
            placeholder="Search..."
            solo
            flat
            @input="searchIt"
            v-model="search"
            :hide-details="true"
          ></v-text-field>
          <table>
            <tr>
              <th class="ps-5">#</th>
              <th>Designation</th>
              <th>Department</th>
              <th class="text-center">Action</th>
            </tr>
            <v-progress-linear
              v-if="loading"
              :active="loading"
              :indeterminate="loading"
              absolute
              color="primary"
            ></v-progress-linear>
            <tr v-for="(item, index) in data" :key="index">
              <td class="ps-5">
                <b>{{ ++index }}</b>
              </td>
              <td>{{ caps(item.name || "---") }}</td>
              <td>
                {{ caps(item.department && item.department.name) }}
              </td>
              <td class="text-center">
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
                    <v-list-item @click="deleteItem(item)">
                      <v-list-item-title style="cursor:pointer">
                        <v-icon color="error" small>
                          mdi-delete
                        </v-icon>
                        Delete
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </td>
            </tr>
          </table>
        </v-card>
        <v-row>
          <v-col md="12" class="float-right">
            <div class="float-right">
              <v-pagination
                v-model="pagination.current"
                :length="pagination.total"
                @input="onPageChange"
                :total-visible="12"
              ></v-pagination>
            </div>
          </v-col>
        </v-row>
        <!-- <v-data-table
          v-if="can(`designation_view`)"
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
          <template v-slot:item.action="{ item }">
            <v-icon
              v-if="can(`designation_edit`)"
              color="secondary"
              small
              class="mr-2"
              @click="editItem(item)"
            >
              mdi-pencil
            </v-icon>
            <v-icon
              v-if="can(`designation_delete`)"
              color="error"
              small
              @click="deleteItem(item)"
            >
              mdi-delete
            </v-icon>
          </template>
          <template v-slot:no-data> </template>
        </v-data-table> -->
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    pagination: {
      current: 1,
      total: 0,
      per_page: 10
    },
    Model: "Designation",
    options: {},
    endpoint: "designation",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      { text: "Designation", align: "left", sortable: false, value: "name" },
      {
        text: "Department",
        align: "left",
        sortable: false,
        value: "department.name"
      },
      { text: "Actions", align: "center", value: "action", sortable: false }
    ],
    editedIndex: -1,
    editedItem: { name: "", department_id: "" },
    defaultItem: { name: "", department_id: "" },
    response: "",
    data: [],
    departments: [],
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
    }
  },
  created() {
    this.getDepartments();
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
    getDepartments() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
      });
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      let page = this.pagination.current;

      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
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
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
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
        name: this.editedItem.name.toLowerCase(),
        department_id: this.editedItem.department_id,
        company_id: this.$auth.user.company.id
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                item => item.id == this.editedItem.id
              );
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch(err => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
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
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 5px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
