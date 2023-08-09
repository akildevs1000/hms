<template>
  <div v-if="can('settings_roles_access') && can('settings_roles_view')">
    <v-dialog v-model="dialogNewRole" width="500">

      <v-card>
        <v-card-title dense class=" primary  white--text background">
          {{ formTitle }} {{ Model }}
          <v-spacer></v-spacer>
          <v-icon @click="dialogNewRole = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-label>Role Name</v-label>
                <v-text-field class="mt-1" outlined dense v-model="editedItem.name"></v-text-field>
                <span v-if="errors && errors.name" class="error--text">
                  {{ errors.name[0] }}</span>
              </v-col>
              <v-col> </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn class="error" small @click="close"> Cancel </v-btn> -->
          <v-btn class="background" dark small @click="save">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <!-- <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
      <v-col cols="6">
        <div class="text-right">

        </div>
      </v-col>
    </v-row> -->

    <v-row>
      <v-col md="12">
        <v-data-table v-model="ids" item-key="id" :headers="headers" :items="data" :loading="loading"
          :options.sync="options" :footer-props="{
            itemsPerPageOptions: [50, 100, 500, 1000],
          }" class="elevation-1">
          <template v-slot:top>

            <v-card class="mb-5 rounded-md mt-3" elevation="0">
              <v-toolbar class="rounded-md" style="border-radius: 5px 5px 0px 0px" color="background" dense flat dark>
                <span> Dashboard / Roles List</span>
                <v-tooltip top color="primary">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                      @click="getDataFromApi()">
                      <v-icon color="white" class="ml-2" dark>mdi mdi-reload</v-icon>
                    </v-btn>
                  </template>
                  <span>Reload</span>
                </v-tooltip>
                <v-spacer></v-spacer>
                <v-toolbar-items>
                  <!-- <v-col>
                    <v-btn v-if="can(`role_deleted`)" small color="error  " class="mr-2 mb-2"
                      @click="delteteSelectedRecords">Delete Selected Records</v-btn>
                  </v-col> -->
                  <v-col>
                    <v-tooltip top color="primary" v-if="can('settings_roles_create')">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on">
                          <v-icon color="white" class="ml-2" @click="dispalyNewDialog()" dark>mdi mdi-plus-circle</v-icon>
                        </v-btn>
                      </template>
                      <span>Add New Role</span>
                    </v-tooltip>
                  </v-col>
                </v-toolbar-items>
              </v-toolbar>
            </v-card>
            <!-- <v-toolbar flat>
              <v-divider class="mx-4" inset vertical></v-divider>
              <v-text-field @input="searchIt" v-model="search" label="Search" single-line hide-details></v-text-field>
            </v-toolbar> -->
          </template>
          <template v-slot:item.name="{ item }">
            {{ item.name }}
          </template>
          <template v-slot:item.action="{ item }">
            <v-icon v-if="can('settings_roles_edit')" color="secondary" small class="mr-2" @click="editItem(item)">
              mdi-pencil
            </v-icon>
            <v-icon v-if="can('settings_roles_delete')" color="error" small @click="deleteItem(item)">
              {{ item.role === "customer" ? "" : "mdi-delete" }}
            </v-icon>
          </template>
          <template v-slot:no-data>
            <!-- <v-btn color="background" @click="initialize">Reset</v-btn> -->
          </template>
        </v-data-table></v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    dialogNewRole: false,
    options: {},
    Model: "Role",
    endpoint: "role",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      { text: "Role", align: "left", sortable: true, key: "name", value: "name" },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
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
      deep: true,
    },
  },
  created() {
    this.loading = true;
  },

  methods: {
    dispalyNewDialog() {
      this.errors = [];
      this.editedItem = { name: "" };
      this.editedIndex = -1;
      this.formTitle = "New";
      this.dialogNewRole = true;
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
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
          role_type: "employee",
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
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
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      //this.dialog = true;
      this.dialogNewRole = true;
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
          .catch((err) => console.log(err));
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
          .catch((err) => console.log(err));
    },

    close() {
      this.dialog = false;
      this.dialogNewRole = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      let payload = {
        name: this.editedItem.name.toLowerCase(),
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
              this.data.splice(index, 1, {
                id: this.editedItem.id,
                name: this.editedItem.name,
              });
              this.snackbar = data.status;
              this.response = data.message;
              this.dialogNewRole = false;
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

