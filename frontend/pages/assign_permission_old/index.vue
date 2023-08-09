<template>
  <div v-if="can(`assign_permission_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Module }}</h3>
        <div>Dashboard / {{ Module }}</div>
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

          <v-btn
            v-if="can(`assign_permission_create`)"
            small
            color="primary"
            to="/assign_permission/create"
            class="mb-2"
            >{{ Module }} +</v-btn
          >
        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="12">
        <v-card elevation="0">
          <div v-for="(item, index) in data" :key="index">
            <v-toolbar class="rounded-md" color="background" dense flat dark>
              <span> {{ item.role && item.role.name }}</span>
            </v-toolbar>
            <table class="mb-15">
              <tr style="text-align:center; ">
                <th style="width:600px; padding: 5px 0 !important">
                  Module
                </th>
                <th>Access</th>
                <th>View</th>
                <th>Create</th>
                <th>Edit</th>
                <th>Delete</th>
              </tr>
              <tr v-for="(items, idx) in permissions" :key="idx">
                <th class="ps-3">{{ capsTitle(idx) }}</th>
                <th
                  v-for="(pa, idx) in items"
                  :key="idx"
                  style="text-align:center !important;"
                  class=""
                >
                  <v-checkbox
                    :value="pa.id"
                    v-model="item.permission_ids"
                    :hide-details="true"
                    class="pt-0  py-1 chk-align"
                  >
                  </v-checkbox>
                </th>
              </tr>
              <v-btn
                v-if="can(`assign_permission_edit`)"
                dark
                small
                color="primary"
                class="mx-1 my-4"
                @click="save(item)"
              >
                Submit
              </v-btn>
              <v-btn
                v-if="can(`assign_permission_delete`)"
                dark
                small
                color="error"
                class="mx-1 my-4"
                @click="deleteItem(item)"
              >
                Delete
              </v-btn>
            </table>
          </div>
        </v-card>
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
    permissions: []
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
    this.loading = true;
    this.getDataFromApi();
    this.getHeaders();

    this.$axios
      .get("dropDownList")
      .then(({ data }) => {
        this.permissions = data.data;
      })
      .catch(err => console.log(err));
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
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

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get(`${url}`, options).then(({ data }) => {
        this.data = data;
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
    save(item) {
      let payload = {
        role_id: item.role_id,
        permission_ids: item.permission_ids
      };
      this.$axios
        .put("assign-permission/" + item.id, payload)
        .then(({ data }) => {
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

body > div {
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

table > thead {
  background-color: #00bcd4;
  color: #fff;
}

table > thead th {
  padding: 15px;
}

table th,
table td {
  border: 1px solid #00000017;
  padding: 10px 15px;
}

table > tbody > tr > td > img {
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

.action_btn > a {
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

.action_btn > a:nth-child(1) {
  border-color: #26a69a;
}

.action_btn > a:nth-child(2) {
  border-color: orange;
}

.action_btn > a:hover {
  box-shadow: 0 3px 8px #0003;
}

table > tbody > tr {
  background-color: #fff;
  transition: 0.3s ease-in-out;
}

table > tbody > tr:nth-child(even) {
  background-color: rgb(238, 238, 238);
}

table > tbody > tr:hover {
  filter: drop-shadow(0px 2px 6px #0002);
}
</style>
