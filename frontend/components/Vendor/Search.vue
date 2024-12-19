<template>
  <v-dialog v-model="searchDialog" width="350" class="empensesvendorsearch">
    <template v-slot:activator="{ on, attrs }">
      <v-hover v-slot:default="{ hover, props }">
        <span v-bind="props">
          <v-icon
            class="zoom-on-hover"
            v-bind="attrs"
            v-on="on"
            :outlined="!hover"
            :color="hover ? `` : `primary`"
            :style="{ color: hover ? '#6946dd' : '' }"
            >mdi-magnify</v-icon
          >
        </span>
      </v-hover>
    </template>
    <v-card :loading="isLoading">
      <v-container>
        <v-autocomplete
          clearable
          v-model="selectedItem"
          :items="filteredItems"
          item-text="fullLabel"
          item-value="id"
          label="Search Vendor Details"
          return-object
          dense
          hide-details
          @change="updateVendorDetails()"
          outlined
          class="empensesvendorsearch2 reports-events-autocomplete"
        >
          <template v-slot:selection="{ attr, on, item, selected }">
            <span v-text="item.first_name + ' ' + item.last_name"></span>
          </template>
          <template
            class="empensesvendorsearch1"
            v-slot:item="{ item }"
            style="max-width: 290px"
          >
            <v-list-item-content
              class="empensesvendorsearch3"
              style="max-width: 290px"
            >
              <v-list-item-title
                v-text="item.first_name + ' ' + item.last_name"
              ></v-list-item-title>
              <v-list-item-subtitle
                v-text="
                  item.mobile +
                  ' , Tax: ' +
                  (item.tax_number && item.tax_number.length > 5
                    ? item.tax_number.toUpperCase()
                    : '---')
                "
              ></v-list-item-subtitle>
              <v-list-item-subtitle
                v-text="item.company_name + ',' + item.type"
              ></v-list-item-subtitle>
              <v-list-item-subtitle
                v-text="item.vendor_category.name + ',' + item.city"
              ></v-list-item-subtitle>
            </v-list-item-content>
          </template>
        </v-autocomplete>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data: () => ({
    selectedItem: null, // The selected item will be stored here
    searchQuery: "", // The search input bound to the autocomplete
    searchDialog: false,
    checkLoader: false,
    search: null, // For search by name or mobile
    search1: null, // For v-autocomplete search input
    isLoading: false, // Loading state for the autocomplete
    items: [], // Items for autocomplete
    model: null, // Bound model for selected vendor
  }),
  watch: {},
  computed: {
    filteredItems() {
      return this.items.map((item) => {
        return {
          ...item,
          fullLabel: `${item.first_name} ${item.last_name} - ${item.mobile} - ${item.tax_number}- ${item.company_name}- ${item.type}- ${item.vendor_category.name}- ${item.city}`, // Custom display label
        };
      });
    },
  },
  methods: {
    // Fetch vendors dynamically based on search input or all if query is empty
    fetchVendors(query = "") {
      this.isLoading = true;

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`vendor-list`, payload)
        .then(({ data }) => {
          if (data) {
            this.items = data; // Populate items with the results

            this.items.map((item) => {
              return {
                ...item,
                fullLabel: `${item.first_name} ${item.last_name} - ${item.mobile} - ${item.tax_number}- ${item.company_name}- ${item.type}- ${item.vendor_category.name}- ${item.city}`, // Custom display label
              };
            });
          } else {
            this.items = [];
          }

          console.log(this.items[0]);

          this.isLoading = false;
        })
        .catch(() => {
          this.isLoading = false;
        });
    },
    updateVendorDetails() {
      if (this.selectedItem) {
        this.$emit("foundVendor", this.selectedItem);
        this.$emit("resetDialog");

        this.searchDialog = false;
        this.checkLoader = false;
        this.selectedItem = null;
      } else {
        alert("Vendor Details are not available");
      }
    },
  },
  created() {
    // Fetch all vendors initially when the dialog is opened, if needed
    this.fetchVendors();
  },
};
</script>
