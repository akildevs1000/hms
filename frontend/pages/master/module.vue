<template>
  <div v-if="can(`module_access`)">
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
            v-if="can(`module_create`)"
            small
            color="primary"
            @click="dialog = true"
            class="mb-2"
            >{{ Module }} +</v-btn
          >
        </div>
      </v-col>
    </v-row>
    <v-data-table
      v-if="can(`module_view`)"
      v-model="ids"
      show-select
      item-key="id"
      :headers="headers"
      :items="data"
      :server-items-length="total"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [50, 100, 500,1000],
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

          <v-dialog v-model="dialog" max-width="500px">
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <v-text-field
                        v-model="editedItem.name"
                        label="Module"
                      ></v-text-field>
                      <span v-if="errors && errors.name" class="error--text">{{
                        errors.name[0]
                      }}</span>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="error" small @click="close"> Cancel </v-btn>
                <v-btn :loading="loading" class="primary" small @click="save"
                  >Save</v-btn
                >
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
    </v-data-table>
    <NoAccess v-else />
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  layout: "master",

  data: () => ({
    Module: "Module",
    options: {},
    endpoint: "module",
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
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? `New ${this.Module}`
        : `Edit ${this.Module}`;
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
        this.getHeaders();
      },
      deep: true,
    },
  },
  created() {
    this.loading = true;
  },

  methods: {
    getHeaders() {
      this.headers = [
        { text: this.Module, align: "left", sortable: false, value: "name" },
        {
          text: "Created At",
          align: "left",
          sortable: false,
          value: "created_at",
        },
      ];
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
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
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
        this.errors = [];
        this.search = "";
        this.ids = [];
        this.loading = false;
      }, 300);
    },

    save() {
      this.loading = true;

      let payload = {
        name: this.editedItem.name.toLowerCase(),
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => this.process(data))
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(({ data }) => this.process(data))
          .catch((res) => console.log(res));
      }
    },
    process(data) {
      this.snackbar = data.status;
      this.response = data.message;

      if (!data.status) {
        this.errors = data.errors;
      } else {
        this.getDataFromApi();
        this.close();
      }
    },
  },
};
</script>
