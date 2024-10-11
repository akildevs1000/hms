<template>
  <div v-if="can(`settings_permissions_access`) && can('settings_permissions_view')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class=" ">
      <v-col cols="6">
        <h3>{{ Module }}</h3>
        <div>Dashboard / {{ Module }}
        </div>
      </v-col>
      <v-col cols="6">
        <div class="text-right">
          <!-- <v-btn
            v-if="can(`assign_permission_delete`)"
            small
            color="error"
            class="mr-2 mb-2"
            @click="delteteSelectedRecords"
            >Delete Selected Records</v-btn
          > -->
          <v-btn v-if="can(`settings_permissions_create`)" small color="primary" to="/assign_permission/create"
            class="mb-2">Assign New Permision +</v-btn>

        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="12">
        <v-row>
          <v-col md="4">
            <span>Select/Change the Role</span>
            <v-autocomplete :rules="Rules" v-model="role_id" :items="roles" item-value="id" item-text="name"
              placeholder="Select Role" outlined dense @change="getDataFromApi()"></v-autocomplete>
            <span v-if="errors && errors.role_id" class="red--text">
              {{ errors.role_id[0] }}
            </span>
          </v-col>
        </v-row>
        <v-card class="mb-5" elevation="0" v-for="(item, index) in  data " :key="index">

          <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
            <v-toolbar-title><span> {{ item.role && capsTitle(item.role.name) }}</span></v-toolbar-title>


            <v-spacer></v-spacer>

            <v-tooltip v-if="can(`settings_permissions_create`)" top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" to="/assign_permission/create">
                  <v-icon dark white>mdi-plus-circle</v-icon>
                </v-btn>
              </template>
              <span>{{ Module }} </span>
            </v-tooltip>

          </v-toolbar>




          <table class="mb-15">

            <tr v-if="can(`settings_permissions_edit`)">
              <th style="width:600px;text-align:center; padding: 5px 0 !important">

              </th>
              <th style="text-align:center;padding-left:50px"> <v-checkbox v-model="access_select_all" label="Select All"
                  @change="setAllIds('access', $event)">
                </v-checkbox> </th>
              <th style="text-align:center;padding-left:50px"> <v-checkbox v-model="view_select_all" label="Select All"
                  @change="setAllIds('view', $event)">
                </v-checkbox></th>
              <th style="text-align:center;padding-left:50px"> <v-checkbox v-model="create_select_all" label="Select All"
                  @change="setAllIds('create', $event)">
                </v-checkbox></th>
              <th style="text-align:center;padding-left:50px"> <v-checkbox v-model="edit_select_all" label="Select All"
                  @change="setAllIds('edit', $event)">
                </v-checkbox></th>
              <th style="text-align:center;padding-left:50px"> <v-checkbox v-model="delete_select_all" label="Select All"
                  @change="setAllIds('delete', $event)">
                </v-checkbox></th>
            </tr>
            <tr style="text-align:center; ">
              <th style="width:600px;text-align:center; padding: 5px 0 !important">
                Module
              </th>
              <th style="text-align:center;padding-left:50px">Access</th>
              <th style="text-align:center;padding-left:50px">View</th>
              <th style="text-align:center;padding-left:50px">Create</th>
              <th style="text-align:center;padding-left:50px">Edit</th>
              <th style="text-align:center;padding-left:50px">Delete</th>

            </tr>
            <tr v-for=" (items, idx) in  permissions " :key="idx">
              <th class="ps-3">{{ capsTitle(idx) }}</th>
              <th v-for="(pa, idx) in  items " :key="idx" style="text-align:center !important;" class="">
                <v-checkbox :disabled="!can(`settings_permissions_edit`)" :value="pa.id" v-model="permission_ids"
                  :hide-details="true" class="pt-0  py-1 chk-align">
                </v-checkbox>

              </th>
            </tr>


          </table>
          <v-col cols="12">
            <span v-if="errors && errors.permission_ids" class="red--text">
              {{ errors.permission_ids[0] }}
            </span>
          </v-col>
          <v-card-actions>
            <v-btn v-if="can(`settings_permissions_delete`)" dark small color="error" class="mx-1 my-4"
              @click="deleteItem(item)">
              Delete
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn v-if="can(`settings_permissions_edit`)" dark small color="primary" class="mx-1 my-4 "
              @click="save(item)">
              Update Permisions
            </v-btn>
          </v-card-actions>
        </v-card>

        <span v-if="data.length == 0"> Data is not avaiallbe. <v-btn v-if="can(`settings_permissions_create`)" small
            color="primary" to="/assign_permission/create" class="mb-2">Assign New Permision +</v-btn></span>
      </v-col>
    </v-row>

    <!-- <v-row>
      <v-col>
        <div class="display-1 pa-2">Assign Permissions</div>
      </v-col>
      <v-col>
        <div class="display-1 pa-2 text-right">
          <v-btn small class="primary" to="/assign_permission">
            <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
          </v-btn>
        </div>
      </v-col>
      <v-col cols="12">
        <v-expansion-panels v-model="panel" :readonly="readonly" multiple>
          <v-expansion-panel v-for="(item, index) in data" :key="index">
            <v-expansion-panel-header>
              <b>{{ item.role.name }}</b>
            </v-expansion-panel-header>
            <v-divider class="p-0 mt-0"></v-divider>
            <v-expansion-panel-content>
              <v-chip
                color="primary ma-2 px-5 py-1"
                v-for="(p, i) in item.permission_names"
                :key="i"
              >
                {{ p }}
              </v-chip>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-col>
    </v-row> -->

    <!-- <v-card elevation="0" class="mb-15">
      <v-form ref="form" lazy-validation>
        <v-card-text>
          <v-container> </v-container>
        </v-card-text>
      </v-form>

      <template v-slot:item.action="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">
          mdi-pencil
        </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
    </v-card> -->
    <!-- <v-data-table
      v-if="can(`assign_permission_view`)"
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
      class="elevation-1 mt-15"
    >
      <template v-slot:top>
        <v-toolbar dark class="primary">{{ Module }}s</v-toolbar>
        <v-toolbar flat color="">
          <v-toolbar-title>List</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-text-field
            @input="searchIt"
            v-model="search"
            label="Search"
            single-line
            hide-details
          ></v-text-field>
        </v-toolbar>
      </template>
      <template v-slot:item.permission_names="{ item }">
        <v-chip
          class="ma-1"
          small
          color="primary"
          v-for="(pa, idx) in item.permission_names"
          :key="idx"
        >
          {{ pa }}
        </v-chip>

        <v-chip class="ma-1" small v-if="item.permission_names.length == 0">
          No permissions assigned
        </v-chip>
      </template>
      <template v-slot:item.action="{ item }">
        <v-icon
          v-if="can(`assign_permission_edit`)"
          color="secondary"
          small
          class="mr-2"
          @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          v-if="can(`assign_permission_delete`)"
          color="error"
          small
          @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
      </template>
      <template v-slot:no-data> </template>
    </v-data-table> -->
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    Rules: [v => !!v || "This field is required"],
    role_id: '',
    roles: [],
    panel: [0, 1, 2],
    readonly: false,
    Module: "Assign Permission",
    options: {},
    endpoint: "assign-permission",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
    permission_ids: [],
    permissions: [],
    access_select_all: false,
    view_select_all: false,
    create_select_all: false,
    edit_select_all: false,
    delete_select_all: false,

  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? `New ${this.Module}`
        : `Edit ${this.Module}`;
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
        this.getHeaders();
      },
      deep: true
    }
  },
  created() {


    this.$axios
      .get("dropDownList")
      .then(({ data }) => {
        this.permissions = data.data;

      })
      .catch(err => console.log(err));

    let options = {
      params: {
        company_id: this.$auth.user.company.id
      }
    };
    this.$axios
      .get("role", options)
      .then(({ data }) => {
        this.roles = data.data;
        this.role_id = this.roles[0].id;
        this.loading = true;
        this.getDataFromApi();
        this.getHeaders();
      })
      .catch(err => console.log(err));
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      if (!u.permissions) return false;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    capsTitle(val) {
      let res = val;
      let r = res.replace(/[^a-z]/g, " ");
      let title = r.replace(/\b\w/g, c => c.toUpperCase());
      return title;
    },

    getHeaders() {
      this.headers = [
        {
          text: "Role",
          align: "left",
          sortable: false,
          value: "role.name"
        },
        {
          text: "Permissions",
          align: "left",
          sortable: false,
          value: "permission_names"
        },
        { text: "Actions", align: "center", value: "action", sortable: false }
      ];
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



          if (isSelected) {
            this.permission_ids.push(...editItemIds);
          }

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
    getDataFromApi(url = this.endpoint) {

      this.access_select_all = false;
      this.view_select_all = false;
      this.create_select_all = false;
      this.edit_select_all = false;
      this.delete_select_all = false;
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get('assign-permission/role-id/' + this.role_id, options).then(({ data }) => {
        this.data = data;
        this.loading = false;
        if (data[0])
          this.permission_ids = data[0].permission_ids;
        // else
        //   this.$router.push("/assign_permission/create");

      });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
    save(item) {
      this.errors = [];
      let payload = {
        role_id: item.role_id,
        permission_ids: this.permission_ids
      };
      this.$axios
        .put("assign-permission/" + item.id, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            return;
          }
          this.response = "Permissions has been assigned";
          this.snackbar = true;
          setTimeout(() => this.$router.push("/assign_permission"), 2000);
        });
    },
    editItem(item) {
      this.$router.push(`assign_permission/${item.id}`);
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
            this.getDataFromApi();
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch(err => console.log(err));
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
  margin-left: 98px !important;
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
