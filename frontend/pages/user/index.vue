<template>
  <div v-if="can(`user_access`)">
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
          <v-btn
            v-if="can(`user_delete`)"
            small
            color="error"
            class="mr-2 mb-2"
            @click="delteteSelectedRecords"
            >Delete Selected Records</v-btn
          >

          <v-btn
            v-if="can(`user_create`)"
            small
            color="primary"
            to="/user/create"
            class="mb-2"
            >{{ Module }} +</v-btn
          >
        </div>
      </v-col>
    </v-row>
    <v-data-table
      v-if="can(`user_view`)"
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
      <template v-slot:item.action="{ item }">
        <v-icon
          v-if="can(`user_edit`)"
          color="secondary"
          small
          class="mr-2"
          @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          v-if="can(`user_delete`)"
          color="error"
          small
          @click="deleteItem(item)"
        >
          {{ item.role === "customer" ? "" : "mdi-delete" }}
        </v-icon>
      </template>
      <template v-slot:no-data>
        <!-- <v-btn color="primary" @click="initialize">Reset</v-btn> -->
      </template>
    </v-data-table>
    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    Module: "User",
    options: {},
    endpoint: "users",
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
    errors: []
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? `New ${this.Module}`
        : `Edit ${this.Module}`;
    }
  },

  watch: {
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
  },

  methods: {
    getHeaders() {
      this.headers = [
        { text: this.Module, align: "left", sortable: false, value: "name" },
        { text: "Email", align: "left", sortable: false, value: "email" },
        { text: "Role", align: "left", sortable: false, value: "role.name" },
        { text: "Actions", align: "center", value: "action", sortable: false }
      ];
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        let items = data.data;

        if (sortBy.length === 1 && sortDesc.length === 1) {
          items = this.sorting(items, sortBy, sortDesc);
        }

        this.data = items;
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

    editItem(item) {
      this.$router.push("/user/" + item.id);
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
              this.response = "Something went wrong.";
            } else {
              this.getDataFromApi();
              this.snackbar = true;
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
              this.snackbar = data.status;
              this.response = data.message;
            } else {
              const index = this.data.indexOf(item);
              this.data.splice(index, 1);
              this.snackbar = data.status;
              this.response = data.message;
            }
          })
          .catch(err => console.log(err));
    }
  }
};
</script>
