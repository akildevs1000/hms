<template>
  <div v-if="can(`department_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt- mb-">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
    </v-row>
    <div>
      <v-dialog v-model="agentDialog" max-width="40%">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <v-toolbar color="background" dense flat dark>
              <span>{{ formTitle }} {{ Model }}</span>
            </v-toolbar>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="agentDialog = false"
              >mdi mdi-close-box</v-icon
            >
          </v-toolbar>
          <v-container>
            <v-row>
              <v-col md="12" lg="12">
                <v-card elevation="0">
                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col md="6" cols="12">
                          <v-text-field
                            v-model="editedItem.contact_name"
                            placeholder="Name"
                            label="Name"
                            outlined
                            :hide-details="true"
                            dense
                          ></v-text-field>
                          <span
                            v-if="errors && errors.contact_name"
                            class="error--text"
                            >{{ errors.contact_name[0] }}</span
                          >
                        </v-col>
                        <v-col md="6" cols="12">
                          <v-text-field
                            v-model="editedItem.name"
                            placeholder="Company"
                            :hide-details="true"
                            outlined
                            label="Company"
                            dense
                          ></v-text-field>
                          <span
                            v-if="errors && errors.name"
                            class="error--text"
                            >{{ errors.name[0] }}</span
                          >
                        </v-col>
                        <v-col md="12" cols="12">
                          <v-select
                            v-model="editedItem.type"
                            :items="sourceTypeList"
                            placeholder="Type"
                            dense
                            label="Type"
                            item-text="name"
                            item-value="value"
                            outlined
                            :hide-details="true"
                          ></v-select>
                          <span
                            v-if="errors && errors.type"
                            class="error--text"
                            >{{ errors.type[0] }}</span
                          >
                        </v-col>
                        <v-col md="6" cols="6">
                          <v-text-field
                            v-model="editedItem.mobile"
                            placeholder="Mobile"
                            label="Mobile"
                            outlined
                            dense
                            :hide-details="true"
                            type="number"
                          ></v-text-field>
                          <span
                            v-if="errors && errors.mobile"
                            class="error--text"
                            >{{ errors.mobile[0] }}</span
                          >
                        </v-col>
                        <v-col md="6" cols="12">
                          <v-text-field
                            v-model="editedItem.landline"
                            placeholder="Landline"
                            outlined
                            label="Landline"
                            dense
                            :hide-details="true"
                            type="number"
                          ></v-text-field>
                          <span
                            v-if="errors && errors.mobile"
                            class="error--text"
                            >{{ errors.mobile[0] }}</span
                          >
                        </v-col>
                        <v-col md="6" cols="12">
                          <v-text-field
                            v-model="editedItem.email"
                            placeholder="Email"
                            outlined
                            label="Email"
                            dense
                            :hide-details="true"
                            type="email"
                          ></v-text-field>
                          <span
                            v-if="errors && errors.email"
                            class="error--text"
                            >{{ errors.email[0] }}</span
                          >
                        </v-col>
                        <v-col md="6" cols="12">
                          <v-text-field
                            v-model="editedItem.gst"
                            placeholder="GST"
                            label="GST"
                            :hide-details="true"
                            outlined
                            dense
                          ></v-text-field>
                          <span
                            v-if="errors && errors.gst"
                            class="error--text"
                            >{{ errors.gst[0] }}</span
                          >
                        </v-col>
                        <v-col md="12" cols="12">
                          <v-text-field
                            v-model="editedItem.address"
                            placeholder="Address"
                            outlined
                            label="Address"
                            textarea
                          ></v-text-field>
                          <span
                            v-if="errors && errors.address"
                            class="error--text"
                            >{{ errors.gst[0] }}</span
                          >
                        </v-col>
                        <v-card-actions>
                          <v-btn class="error" @click="agentDialog = false">
                            Cancel
                          </v-btn>
                          <v-btn class="primary" @click="save">Save</v-btn>
                        </v-card-actions>
                      </v-row>
                    </v-container>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <div>
        <v-row>
          <v-col md="12" lg="12">
            <v-card class="mb-5 rounded-md" elevation="0">
              <v-toolbar class="rounded-md" color="background" dense flat dark>
                <span> {{ Model }} List</span>
                <v-spacer></v-spacer>
                <v-btn
                  class="float-right py-3"
                  @click="agentDialog = true"
                  x-small
                  color="primary"
                >
                  <v-icon color="white" small class="py-5">mdi-plus</v-icon>
                  Add Travel Agent
                </v-btn>
              </v-toolbar>
              <v-text-field
                class="pa-2"
                placeholder="Search..."
                flat
                dense
                outlined
                @input="searchIt"
                v-model="search"
                :hide-details="true"
                style="width: 250px"
              ></v-text-field>
              <table class="mt-0">
                <tr style="font-size: 13px">
                  <th class="ps-5">#</th>
                  <th>Name</th>
                  <th>Company</th>
                  <th>Type</th>
                  <th>Mobile</th>
                  <th>Landline</th>
                  <th>Email</th>
                  <th>GST</th>
                  <th>Address</th>
                  <th>Date</th>
                  <th class="text-center">Action</th>
                </tr>
                <v-progress-linear
                  v-if="loading"
                  :active="loading"
                  :indeterminate="loading"
                  absolute
                  color="primary"
                ></v-progress-linear>
                <tr
                  v-for="(item, index) in data"
                  :key="index"
                  style="font-size: 13px"
                >
                  <!-- <td>{{ caps(item.id) }}</td> -->
                  <td>
                    {{
                      (pagination.current - 1) * pagination.per_page + index + 1
                    }}
                  </td>
                  <td>{{ caps(item.contact_name) }}</td>
                  <td>{{ caps(item.name) }}</td>
                  <td>{{ caps(item.type) }}</td>
                  <td>{{ caps(item.mobile) }}</td>
                  <td>{{ caps(item.landline) }}</td>
                  <td>{{ caps(item.email) }}</td>
                  <td>{{ caps(item.gst) }}</td>
                  <td>{{ caps(item.address) }}</td>
                  <td>
                    {{ item.created_at }}
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
                          <v-list-item-title style="cursor: pointer">
                            <v-icon color="secondary" small>
                              mdi-pencil
                            </v-icon>
                            Edit
                          </v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="deleteItem(item)">
                          <v-list-item-title style="cursor: pointer">
                            <v-icon color="error" small> mdi-delete </v-icon>
                            Delete
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </td>
                </tr>
              </table>
            </v-card>
          </v-col>
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
      </div>
    </div>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    Model: "Sources",
    options: {},
    endpoint: "source",
    search: "",
    snackbar: false,
    dialog: false,
    agentDialog: false,
    ids: [],
    loading: false,
    total: 0,

    editedIndex: -1,
    editedItem: {
      contact_name: "",
      address: "",
      gst: "",
      mobile: "",
      name: "",
      type: "",
      email: "",
      landline: "",
    },
    defaultItem: {
      contact_name: "",
      address: "",
      gst: "",
      mobile: "",
      name: "",
      type: "",
      email: "",
      landline: "",
    },
    response: "",
    data: [],
    sourceTypeList: [
      {
        name: "Online",
        value: "Online",
      },
      {
        name: "Travel Agent",
        value: "Agent",
      },
    ],
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    agentDialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
  },

  created() {
    this.loading = true;
    this.getDataFromApi();
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },
    searchIt(e) {
      let s = e.toLowerCase();
      this.getDataFromApi();
      return;

      if (s.length == 0) {
        this.getDataFromApi();
      } else if (s.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${s}`);
      }
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);

      console.log(item);

      this.agentDialog = true;
    },

    delteteSelectedRecords() {
      let just_ids = this.ids.map((e) => e.id);
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: just_ids,
          })
          .then((res) => {
            if (!res.data.status) {
              this.errors = res.data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = res.data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
          })
          .catch((err) => console.log(err));
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
          .catch((err) => console.log(err));
    },

    close() {
      this.agentDialog = false;

      // setTimeout(() => {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
      // }, 300);
    },

    save() {
      let payload = {
        name: this.editedItem.name.toLowerCase(),
        type: this.editedItem.type.toLowerCase(),
        gst: this.editedItem.gst,
        address: this.editedItem.address,
        mobile: this.editedItem.mobile,
        email: this.editedItem.email,
        landline: this.editedItem.landline,
        contact_name: this.editedItem.contact_name.toLowerCase(),
        company_id: this.$auth.user.company.id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
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
          .catch((res) => console.log(res));
      }
    },
  },
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

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}
</style>
