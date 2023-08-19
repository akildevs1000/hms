<template>
  <div v-if="can('settings_roles_access') && can('settings_roles_view')">
    <v-dialog v-model="dialogNewRole" width="60%">

      <v-card>
        <v-card-title dense class=" primary  white--text background">
          {{ formTitle }} {{ Model }} and Permissions
          <v-spacer></v-spacer>
          <v-icon @click="dialogNewRole = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="4">
                <v-label>Role Name</v-label>
                <v-text-field class="mt-1" outlined dense v-model="editedItem.name"
                  placeholder="Enter New Role Name"></v-text-field>
                <span v-if="errors && errors.name" class="error--text">
                  {{ errors.name[0] }}</span>
              </v-col>
              <v-col cols="4">
                <v-label>Role Description</v-label>
                <v-text-field class="mt-1" outlined dense v-model="editedItem.description"
                  placeholder="Enter Role Description"></v-text-field>
                <span v-if="errors && errors.description" class="error--text">
                  {{ errors.description[0] }}</span>
              </v-col>


              <v-col cols="12">

                <table class="mb-15">
                  <tr style="text-align:center; ">
                    <th style="width:600px;text-align:center; padding: 5px 0 !important">
                      Module
                    </th>
                    <th style="text-align:center;padding-left:10px"> <v-checkbox label="Access"
                        @change="setAllIds('access', $event)" v-model="selectall1">
                      </v-checkbox> </th>
                    <th style="text-align:center;padding-left:10px"> <v-checkbox label="View"
                        @change="setAllIds('view', $event)" v-model="selectall2">
                      </v-checkbox></th>
                    <th style="text-align:center;padding-left:10px"> <v-checkbox label="Create"
                        @change="setAllIds('create', $event)" v-model="selectall3">
                      </v-checkbox></th>
                    <th style="text-align:center;padding-left:10px"> <v-checkbox label="Edit"
                        @change="setAllIds('edit', $event)" v-model="selectall4">
                      </v-checkbox></th>
                    <th style="text-align:center;padding-left:10px"> <v-checkbox label="Delete"
                        @change="setAllIds('delete', $event)" v-model="selectall5">
                      </v-checkbox></th>
                  </tr>
                  <tr v-for="(items, idx) in permissions" :key="idx">
                    <td class="ps-3">{{ capsTitle(idx) }}</td>
                    <td v-for="(pa, idx) in items" :key="idx" style="text-align:center;padding-left:50px" class="">
                      <v-checkbox :disabled="!can(`settings_permissions_create`)" :value="pa.id" v-model="permission_ids"
                        :hide-details="true" class="pt-0  py-1 chk-align">
                      </v-checkbox>
                    </td>
                  </tr>
                </table>


              </v-col>
              <v-col cols="6" class="text-right">
                <span v-if="errors && errors.permission_ids" class="red--text">
                  {{ errors.permission_ids[0] }}
                </span>
                <span v-if="errors && errors.name" class="error--text">
                  {{ errors.name[0] }}</span>
                <span v-if="errors && errors.description" class="error--text">
                  {{ errors.description[0] }}</span>
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
      { text: "Description", align: "left", sortable: true, key: "description", value: "description" },
      { text: "Created", align: "left", sortable: true, key: "updated_at", value: "updated_at" },
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
    formTitle: 'New',
    selectall1: '',
    selectall2: '',
    selectall3: '',
    selectall4: '',
    selectall5: '',
    editPermissionId: '',
  }),

  computed: {

  },

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
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get('assign-permission/role-id/' + item.id, options).then(({ data }) => {
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
        name: this.editedItem.name.toLowerCase(),
        description: this.editedItem.description.toLowerCase(),

        company_id: this.$auth.user.company.id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {

              if (this.editPermissionId == '') {
                this.savePermisions(this.editedItem.id);
              }
              else {
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
        .catch(err => console.log(err));
    },
    updatePermission(role_id) {
      //alert(this.editPermissionId);
      console.log(this.editPermissionId);
      let payload = {
        role_id: role_id,
        permission_ids: this.permission_ids
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
        .catch(err => console.log(err));
    },
    capsTitle(val) {
      if (val == 'gst') { val = val.toUpperCase(); return val; }
      let res = val;
      let r = res.replace(/[^a-z]/g, " ");
      let title = r.replace(/\b\w/g, c => c.toUpperCase());
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
          const editItems = data[module].filter(item => item.name.includes(filter));

          // Extract and store the IDs from the filtered items
          const editItemIds = editItems.map(item => item.id);

          if (isSelected)
            this.permission_ids.push(...editItemIds);
          else {

            const indexToDelete = this.permission_ids.findIndex(item => item === editItemIds[0]);

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
        role_id: role_id,//this.role_id,
        permission_ids: this.permission_ids,
        company_id: this.$auth.user.company.id
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
    }
  },



};
</script>


<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

th,
td {
  border: 1px solid #dddddd;
  /* text-align: center; */
  padding: 5px 5px;
}

.chk-align {
  text-align: center !important;
  margin-top: 8px !important;
  /* margin-left: 98px !important;*/
}

* {
  box-sizing: border-box;
}

body>div {
  min-height: 100vh;
  display: flex;
  font-family: "Roboto", sans-serif;
}

.table_responsive {
  max-width: 900px;
  border: 1px solid #00bcd4;
  background-color: #efefef33;
  padding: 15px;
  overflow: auto;
  margin: auto;
  border-radius: 4px;
}

table {
  width: 100%;
  font-size: 13px;
  color: #444;
  white-space: nowrap;
  border-collapse: collapse;
}

table>thead {
  background-color: #00bcd4;
  color: #fff;
}

table>thead th {
  padding: 15px;
}

table th,
table td {
  border: 1px solid #00000017;
  padding: 0px 0px;
}

table>tbody>tr>td>img {
  display: inline-block;
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid #fff;
  box-shadow: 0 2px 6px #0003;
}

.action_btn {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.action_btn>a {
  text-decoration: none;
  color: #444;
  background: #fff;
  border: 1px solid;
  display: inline-block;
  padding: 7px 20px;
  font-weight: bold;
  border-radius: 3px;
  transition: 0.3s ease-in-out;
}

.action_btn>a:nth-child(1) {
  border-color: #26a69a;
}

.action_btn>a:nth-child(2) {
  border-color: orange;
}

.action_btn>a:hover {
  box-shadow: 0 3px 8px #0003;
}

table>tbody>tr {
  background-color: #fff;
  transition: 0.3s ease-in-out;
}

table>tbody>tr:nth-child(even) {
  background-color: rgb(238, 238, 238);
}

table>tbody>tr:hover {
  filter: drop-shadow(0px 2px 6px #0002);
}
</style>


