<template>
  <div v-if="can('master')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>device</h3>
        <div>Dashboard / device</div>
      </v-col>
      <v-col cols="6">
        <div class="text-right">
          <v-btn
            small
            color="error"
            class="mr-2 mb-2"
            @click="delteteSelectedRecords"
            >Delete Selected Records</v-btn
          >

          <v-btn small color="primary" to="/device/create" class="mb-2"
            >device +</v-btn
          >
        </div>
      </v-col>
    </v-row>
    <v-data-table
      v-model="ids"
      show-select
      item-key="id"
      :headers="headers"
      :items="devices"
      :server-items-length="total"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [50, 100, 500, 1000]
      }"
      class="elevation-1"
    >
      <template v-slot:top>
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
      <template v-slot:item.action="{ item }">
        <v-icon color="secondary" small class="mr-2" @click="editItem(item.id)">
          mdi-pencil
        </v-icon>
        <v-icon color="error" small @click="deleteItem(item)">
          {{ item.device === "customer" ? "" : "mdi-delete" }}
        </v-icon>
      </template>
      <template v-slot:no-data>
        <!-- <v-btn color="primary" @click="initialize">Reset</v-btn> -->
      </template>
    </v-data-table>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  layout({ $auth }) {
    let { user_type } = $auth.user;
    if (user_type == "master") {
      return "master";
    } else if (user_type == "employee") {
      return "employee";
    } else if (user_type == "master") {
      return "default";
    }
  },
  data: () => ({
    options: {},
    endpoint: "device",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      { text: "Name", align: "left", value: "name", sortable: false },
      { text: "Device Id", align: "left", value: "device_id", sortable: false },
      { text: "Status", align: "left", value: "status.name", sortable: false },
      {
        text: "Company",
        align: "left",
        value: "company.name",
        sortable: false
      },
      { text: "Location", align: "left", value: "location", sortable: false },

      { text: "Actions", align: "center", value: "action", sortable: false }
    ],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    devices: [],
    errors: []
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New device" : "Edit device";
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
      },
      deep: true
    }
  },
  created() {
    this.loading = true;
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      return u && u.user_type == per;
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user?.company?.id
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        let items = data.data;

        if (sortBy.length === 1 && sortDesc.length === 1) {
          items = this.sorting(items, sortBy, sortDesc);
        }

        this.devices = items;
        this.total = data.total;
        this.loading = false;
      });
    },

    sorting(items, sortBy, sortDesc) {
      return items.sort((a, b) => {
        const sortA = a[sortBy[0]];
        const sortB = b[sortBy[0]];

        if (sortDesc[0]) {
          if (sortA < sortB) return 1;
          if (sortA > sortB) return -1;
          return 0;
        } else {
          if (sortA < sortB) return -1;
          if (sortA > sortB) return 1;
          return 0;
        }
      });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    editItem(id) {
      this.$router.push(`/master/device/${id}`);
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
            const index = this.devices.indexOf(item);
            this.devices.splice(index, 1);
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
        name: this.editedItem.name.toLowerCase()
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(res => {
            if (!res.data.status) {
              this.errors = res.data.errors;
            } else {
              const index = this.devices.findIndex(
                item => item.id == this.editedItem.id
              );
              this.devices.splice(index, 1, {
                id: this.editedItem.id,
                name: this.editedItem.name
              });
              this.snackbar = res.data.status;
              this.response = res.data.message;
              this.close();
            }
          })
          .catch(err => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(res => {
            if (!res.data.status) {
              this.errors = res.data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = res.data.status;
              this.response = res.data.message;
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
