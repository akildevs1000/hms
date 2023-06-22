<template>
  <div>
    <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify" v-model="search"
      @input="applyFilters"></v-text-field>

    <v-btn color="success" @click="toggleFilter">Toggle Filters</v-btn>

    <v-data-table :headers="headers" :items="filteredItems" :total-items="totalItems" :loading="loading"
      class="elevation-1" @pagination="fetchData">
      <template v-slot:header="{ props: { headers } }">
        <tr v-if="isFilter">
          <th v-for="header in headers" :key="header.text">
            <v-text-field v-if="header.filterable" v-model="filters[header.value]" :label="header.text" clearable
              @input="applyFilters" dense outlined flat append-icon="mdi-magnify"></v-text-field>
            <template v-else>
              {{ header.text }}
            </template>
          </th>
        </tr>
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isFilter: false,
      items: [],
      headers: [
        { text: 'Name', value: 'name', filterable: true },
        { text: 'Age', value: 'age', filterable: true },
        { text: 'City', value: 'city', filterable: true }
      ],
      filters: {},
      search: '',
      currentPage: 1,
      perPage: 10,
      totalItems: 0,
      loading: false
    };
  },
  computed: {
    filteredItems() {
      // Return the filtered items based on filters and search query
      // You can fetch this data from the server based on the filters and search query
      return this.items;
    }
  },
  methods: {
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    applyFilters() {
      // Reset the current page when filters or search query change
      this.currentPage = 1;
      this.fetchData();
    },
    fetchData() {
      // Fetch the data from the server based on filters, search query, currentPage, and perPage
      // Update the items and totalItems properties accordingly
      // You need to make an API request to your server with the filter parameters and pagination parameters
      // Set the loading state accordingly
      // Example API request:

      let options = {
        params: {
          filters: this.filters,
          search: this.search,
          page: this.currentPage,
          perPage: this.perPage,
          company_id: this.$auth.user.company.id,
          // search: this.search,
        },
      };

      this.$axios.get('get_room_type_list', options)
        .then(response => {
          this.items = response.data.items;
          this.totalItems = response.data.totalItems;
          this.loading = false;
        })
        .catch(error => {
          console.error(error);
          this.loading = false;
        });

      // Simulating API request with setTimeout
      this.loading = true;
      setTimeout(() => {
        const filteredItems = this.filterData(); // Simulating filtering on the frontend
        const startIndex = (this.currentPage - 1) * this.perPage;
        const endIndex = startIndex + this.perPage;
        this.items = filteredItems.slice(startIndex, endIndex);
        this.totalItems = filteredItems.length;
        this.loading = false;
      }, 1000);
    },
    filterData() {
      // Apply filtering logic on the items array based on the filters and search query
      // You can modify this method based on your data structure and filtering requirements
      return this.items.filter(item => {
        for (const key in this.filters) {
          if (this.filters[key] && String(item[key]).toLowerCase().indexOf(this.filters[key].toLowerCase()) === -1) {
            return false;
          }
        }
        return true;
      });
    }
  },
  mounted() {
    this.fetchData();
  }
};
</script>
