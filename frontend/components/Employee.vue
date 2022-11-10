<template>
  <v-data-table
    v-model="ids"
    show-select
    item-key="id"
    :headers="headers"
    :items="data"
    :server-items-length="total"
    :loading="loading"
    :options.sync="options"
    :footer-props="{
      itemsPerPageOptions: [50, 100, 500,1000]
    }"
    class="elevation-1"
  >
  </v-data-table>
</template>
<script>
export default {
  data: () => ({
    Module: "Employee",
    options: {},
    endpoint: "employee",
    dialog_search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      {
        text: "First Name",
        align: "left",
        sortable: false,
        value: "first_name"
      }
    ],
    response: "",
    data: [],
    errors: []
  }),

  computed: {},

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.dialog_search = "";
    },
    options: {
      handler() {
        this.getEmployeesDataFromApi();
      },
      deep: true
    }
  },
  methods: {
    getEmployeesDataFromApi(url = this.endpoint) {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          page: page,
          per_page: itemsPerPage
        }
      };

      this.$axios.get(url, options).then(({ data }) => {
        let items = data.data;

        this.data = items;
        this.total = data.total;
        this.loading = false;
      });
    },

    dialog_searchIt(e) {
      if (e.length == 0) {
        this.getEmployeesDataFromApi();
      } else if (e.length > 2) {
        this.getEmployeesDataFromApi(`${this.endpoint}/dialog_search/${e}`);
      }
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
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
              this.getEmployeesDataFromApi();
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
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
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
              const index = this.data.findIndex(
                item => item.id == this.editedItem.id
              );
              this.data.splice(index, 1, {
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
              this.getEmployeesDataFromApi();
              this.snackbar = res.data.status;
              this.response = res.data.message;
              this.close();
              this.errors = [];
              this.dialog_search = "";
            }
          })
          .catch(res => console.log(res));
      }
    }
  }
};
</script>
