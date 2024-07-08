<template>
  <div v-if="can('settings_roles_access') && can('settings_roles_view')">
    <v-dialog v-model="dialogNewRole" width="60%">
      <v-card>
        <v-toolbar dense flat class="primary white--text">
          {{ formTitle }} {{ Model }} and Permissions
          <v-spacer></v-spacer>
          <v-icon @click="dialogNewRole = false" outlined dark color="white">
            mdi mdi-close
          </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-container>
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
                <table class="mb-15">
                  <tr>
                    <td><b>Module</b></td>
                    <td class="text-center">
                      <v-checkbox
                        dense
                        hide-details
                        label="Access"
                        @change="setAllIds('access', $event)"
                        v-model="selectall1"
                      >
                      </v-checkbox>
                    </td>
                    <td class="text-center">
                      <v-checkbox
                        dense
                        hide-details
                        label="View"
                        @change="setAllIds('view', $event)"
                        v-model="selectall2"
                      >
                      </v-checkbox>
                    </td>
                    <td class="text-center">
                      <v-checkbox
                        dense
                        hide-details
                        label="Create"
                        @change="setAllIds('create', $event)"
                        v-model="selectall3"
                      >
                      </v-checkbox>
                    </td>
                    <td class="text-center">
                      <v-checkbox
                        dense
                        hide-details
                        label="Edit"
                        @change="setAllIds('edit', $event)"
                        v-model="selectall4"
                      >
                      </v-checkbox>
                    </td>
                    <td class="text-center">
                      <v-checkbox
                        dense
                        hide-details
                        label="Delete"
                        @change="setAllIds('delete', $event)"
                        v-model="selectall5"
                      >
                      </v-checkbox>
                    </td>
                  </tr>
                  <tr v-for="(items, idx) in permissions" :key="idx">
                    <td>{{ capsTitle(idx) }}</td>

                    <td v-for="(pa, idx) in items" :key="idx">
                      <div
                        class="d-flex align-center justify-center"
                        style="height: 100%"
                      >
                        <v-checkbox
                          :disabled="!can(`settings_permissions_create`)"
                          :value="pa.id"
                          v-model="permission_ids"
                          dense
                          hide-details
                        ></v-checkbox>
                      </div>
                    </td>
                  </tr>
                </table>
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
            </v-row>
            <v-card-actions>
              <v-spacer></v-spacer>
              <!-- <v-btn class="error" small @click="close"> Cancel </v-btn> -->
              <v-btn color="primary" fill small @click="save">Save</v-btn>
            </v-card-actions>
          </v-container>
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
                      v-bind="attrs"
                      v-on="on"
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
    selectall1: "",
    selectall2: "",
    selectall3: "",
    selectall4: "",
    selectall5: "",
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
      this.errors = [];
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      //this.dialog = true;
      this.dialogNewRole = true;

      this.selectall1 = false;
      this.selectall2 = false;
      this.selectall3 = false;
      this.selectall4 = false;
      this.selectall5 = false;
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
    setAllIds(filter, e) {
      let isSelected = e;

      // this.permission_ids = this.just_ids
      //   ? this.permissions.map(e => e.id)
      //   : [];

      // Function to filter IDs containing the text "edit"
      let data = this.permissions;
      const filteredIds = [];

      // Loop through each module in the data
      for (const module in data) {
        if (data.hasOwnProperty(module)) {
          // Filter the items in the module where the "name" contains "edit"
          const editItems = data[module].filter((item) =>
            item.name.includes(filter)
          );

          // Extract and store the IDs from the filtered items
          const editItemIds = editItems.map((item) => item.id);

          if (isSelected) this.permission_ids.push(...editItemIds);
          else {
            const indexToDelete = this.permission_ids.findIndex(
              (item) => item === editItemIds[0]
            );

            if (indexToDelete !== -1) {
              this.permission_ids.splice(indexToDelete, 1);
            }
          }
        }
      }
      const uniqueSet = new Set(this.permission_ids);
      this.permission_ids = Array.from(uniqueSet);
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

<style scoped>
@import url("../assets/css/tableStyles.css");
</style>
