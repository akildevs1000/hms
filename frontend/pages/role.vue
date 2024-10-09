<template>
  <div v-if="can('settings_roles_access') && can('settings_roles_view')">
    <v-dialog persistent v-model="dialogNewRole" width="900">
      <AssetsIconClose left="890" @click="closeDialog" />
      <v-card>
        <v-alert dense flat class="grey lighten-3">
          {{ formTitle }} {{ Model }} and Permissions
          <v-spacer></v-spacer>
        </v-alert>
        <v-card-text>
          <v-row>
            <v-col cols="5">
              <v-text-field
                hide-details
                label="Role Name"
                outlined
                dense
                v-model="editedItem.name"
              ></v-text-field>
              <span v-if="errors && errors.name" class="error--text">
                {{ errors.name[0] }}</span
              >
            </v-col>
            <v-col cols="5">
              <v-text-field
                hide-details
                label="Role Description"
                outlined
                dense
                v-model="editedItem.description"
              ></v-text-field>
              <span v-if="errors && errors.description" class="error--text">
                {{ errors.description[0] }}</span
              >
            </v-col>
            <v-col cols="2" class="text-right">
              <v-btn color="primary" fill small @click="save">Save</v-btn>
            </v-col>

            <v-col cols="12">
              <Accordian
                :defaultPermissionsIds="
                  editedIndex === -1 ? [] : permission_ids
                "
                :permissions="permissions"
                @selectedPermissions="handleSelectedPermissions"
              />
            </v-col>
            <v-col cols="6" class="text-right">
              <span v-if="errors && errors.permission_ids" class="red--text">
                {{ errors.permission_ids[0] }}
              </span>
              <span v-if="errors && errors.name" class="error--text">
                {{ errors.name[0] }}</span
              >
              <span v-if="errors && errors.description" class="error--text">
                {{ errors.description[0] }}</span
              >
            </v-col>
            <v-col class="text-right">
              <v-btn color="primary" fill small @click="save">Save</v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row>
      <v-col md="12">
        <v-data-table
          v-model="ids"
          item-key="id"
          :headers="headers"
          :items="data"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [50, 100, 500, 1000],
          }"
          class="elevation-1"
        >
          <template v-slot:top>
            <v-card class="mb-5 rounded-md mt-3" elevation="0">
              <v-toolbar class="rounded-md" dense flat>
                <span> Roles</span>
                <v-tooltip top color="primary">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      dense
                      class="ma-0 px-0"
                      x-small
                      :ripple="false"
                      text
                      v-bind="attrs"
                      v-on="on"
                      @click="getDataFromApi()"
                    >
                      <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
                    </v-btn>
                  </template>
                  <span>Reload</span>
                </v-tooltip>
                <v-spacer></v-spacer>
                <v-toolbar-items>
                  <v-col>
                    <v-btn
                      v-if="can('settings_roles_create')"
                      dark
                      dense
                      color="blue"
                      small
                      @click="dispalyNewDialog()"
                    >
                      <v-icon color="white" small> mdi-plus </v-icon>
                      Role
                    </v-btn>
                  </v-col>
                </v-toolbar-items>
              </v-toolbar>
            </v-card>
          </template>

          <template v-slot:item.action="{ item }">
            <v-icon
              v-if="can('settings_roles_edit')"
              color="secondary"
              small
              class="mr-2"
              @click="editItem(item)"
            >
              mdi-pencil
            </v-icon>
            <v-icon
              v-if="can('settings_roles_delete')"
              color="error"
              small
              @click="deleteItem(item)"
            >
              {{ item.role === "customer" ? "" : "mdi-delete" }}
            </v-icon>
          </template>
          <template v-slot:no-data>
            <!-- <v-btn color="background" @click="initialize">Reset</v-btn> -->
          </template>
        </v-data-table></v-col
      >
    </v-row>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    compKey: 1,
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
      {
        text: "Role",
        align: "left",
        sortable: true,
        key: "name",
        value: "name",
      },
      {
        text: "Description",
        align: "left",
        sortable: true,
        key: "description",
        value: "description",
      },
      {
        text: "Created",
        align: "left",
        sortable: true,
        key: "updated_at",
        value: "updated_at",
      },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: { name: "", description: "" },
    defaultItem: { name: "", description: "" },
    response: "",
    data: [],
    errors: [],

    permission_ids: [],
    permissions: [],
    formTitle: "New",
    editPermissionId: "",
  }),

  computed: {},

  watch: {
    editedIndex(val) {
      this.formTitle = val === -1 ? "New" : "Edit";
    },
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

    //permissions
    this.getPermissions();
  },

  methods: {
    closeDialog() {
      this.dialogNewRole = false;
      ++this.compKey;
    },
    handleSelectedPermissions(e) {
      this.permission_ids = e;
    },
    dispalyNewDialog() {
      this.errors = [];
      this.editedItem = { name: "", description: "" };
      this.editedIndex = -1;
      this.formTitle = "New";
      this.dialogNewRole = true;
      this.permission_ids = [];
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
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
      console.log(item);
      this.errors = [];
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      //this.dialog = true;
      this.dialogNewRole = true;

      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get("assign-permission/role-id/" + item.id, options)
        .then(({ data }) => {
          //this.data = data;
          this.loading = false;
          if (data[0]) {
            this.permission_ids = data[0].permission_ids;
            this.editPermissionId = data[0].id;

            //alert(this.editPermissionId);
          }

          // else
          //   this.$router.push("/assign_permission/create");
        });
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
              this.deletePermission();

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
        name: this.editedItem.name,
        description: this.editedItem.description,

        company_id: this.$auth.user.company.id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              if (this.editPermissionId == "") {
                this.savePermisions(this.editedItem.id);
              } else {
                this.updatePermission(this.editedItem.id);
              }

              this.getDataFromApi();
              // const index = this.data.findIndex(
              //   (item) => item.id == this.editedItem.id
              // );
              // this.data.splice(index, 1, {
              //   id: this.editedItem.id,
              //   name: this.editedItem.name,
              // });
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
              this.savePermisions(data.record.id);
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
    //permissions
    deletePermission(id) {
      this.$axios
        .delete(this.endpoint + "/" + id)
        .then(({ data }) => {
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },
    updatePermission(role_id) {
      //alert(this.editPermissionId);
      console.log(this.editPermissionId);
      let payload = {
        role_id: role_id,
        permission_ids: this.permission_ids,
      };
      this.$axios
        .put("assign-permission/" + this.editPermissionId, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            return;
          }
          this.response = "Permissions has been assigned";
          this.snackbar = true;
          //setTimeout(() => this.$router.push("/assign_permission"), 2000);
        });
    },

    getPermissions(url = "dropDownList") {
      this.$axios
        .get(url)
        .then(({ data }) => {
          this.permissions = data.data;
        })
        .catch((err) => console.log(err));
    },
    capsTitle(val) {
      if (val == "gst") {
        val = val.toUpperCase();
        return val;
      }
      let res = val;
      let r = res.replace(/[^a-z]/g, " ");
      let title = r.replace(/\b\w/g, (c) => c.toUpperCase());
      return title;
    },

    savePermisions(role_id) {
      this.errors = [];
      let payload = {
        role_id: role_id, //this.role_id,
        permission_ids: this.permission_ids,
        company_id: this.$auth.user.company.id,
      };

      this.$axios.post("assign-permission", payload).then(({ data }) => {
        if (!data.status) {
          this.errors = data.errors;
          return;
        }
        this.msg = "Permissions has been assigned";
        this.snackbar = true;
        //setTimeout(() => this.$router.push("/assign_permission"), 1000);
      });
    },
  },
};
</script>
