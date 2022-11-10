<template>
  <div v-if="can('master')">
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
            small
            color="primary"
            to="/master/assign_module/create"
            class="mb-2"
            >{{ Module }} +</v-btn
          >
        </div>
      </v-col>
    </v-row>
    <v-data-table
      v-model="ids"
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
      <template v-slot:item.module_names="{ item }">
        <v-chip
          class="ma-1"
          small
          color="primary"
          v-for="(module, idx) in item.module_names"
          :key="idx"
        >
          {{ module }}
        </v-chip>

        <v-chip class="ma-1" small v-if="!item.module_names.length">
          No modules assigned
        </v-chip>
      </template>
      <template v-slot:item.action="{ item }">
        <v-icon color="secondary" small class="mr-2" @click="editItem(item)">
          mdi-pencil
        </v-icon>
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
    Module: "Assign Module",
    options: {},
    endpoint: "assign-module",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: true,
    total: 0,
    headers: [
      { text: "Id", align: "left", sortable: false, value: "id" },
      {
        text: "Company Id",
        align: "left",
        sortable: false,
        value: "company.id"
      },
      {
        text: "Company",
        align: "left",
        sortable: false,
        value: "company.name"
      },
      {
        text: "Modules",
        align: "left",
        sortable: false,
        value: "module_names"
      },
      { text: "Actions", align: "center", value: "action", sortable: false }
    ],
    response: "",
    data: [],
    errors: []
  }),
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true
    }
  },

  created() {
    this.getDataFromApi();
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      return u && u.user_type == per;
    },

    getDataFromApi(url = this.endpoint) {
      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage
        }
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.loading = false;
        this.data = data.data;
        this.total = data.total;
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
      this.$router.push(`/master/assign_module/${item.id}`);
    }
  }
};
</script>
