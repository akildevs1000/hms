<template>
  <span>
    <v-data-table
      dense
      :headers="headers"
      :items="vendors"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [100, 500, 1000],
      }"
      class="elevation-1 pa-3"
    >
      <template v-slot:top>
        <v-toolbar flat dense class="mb-5">
          {{ Model }}
          <v-icon color="primary" right class="mt-1" @click="getDataFromApi()"
            >mdi-reload</v-icon
          >
          <v-spacer></v-spacer>
          <VendorCreate
            :model="Model"
            :endpoint="endpoint"
            @response="getDataFromApi"
          />
        </v-toolbar>
      </template>
      <template v-slot:item.full_name="{ item }">
        {{ item.first_name }} {{ item.last_name }}
      </template>
      <template v-slot:item.company="{ item }">
        <div>
          {{ item.company_name }}
        </div>
        <small>{{ item.email }}</small>
        <br />
        <small>
          {{ item.address }}
        </small>
      </template>
      <template v-slot:item.contact="{ item }">
        <div>
          {{ item.work_phone }}
        </div>
        <div>
          {{ item.mobile }}
        </div>
      </template>
      <template v-slot:item.tax_number="{ item }">
        <v-icon v-if="item.tax_number" color="green">mdi-check</v-icon>
        <v-icon v-else color="red">mdi-close</v-icon>
      </template>

      <template v-slot:item.options="{ item }">
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <div class="text-center">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </div>
          </template>
          <v-list width="120" dense>
            <v-list-item>
              <v-list-item-title>
                <VendorView
                  :model="Model"
                  :endpoint="endpoint"
                  :item="item"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <VendorEdit
                  :model="Model"
                  :endpoint="endpoint"
                  :item="item"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <VendorDelete
                  :id="item.id"
                  :endpoint="endpoint"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
    </v-data-table>
  </span>
</template>

<script>
let date = new Date();

let d = date.getDate();
let m = (date.getMonth() + 1).toString().padStart(2, "0");
let y = date.getFullYear();
let currentDate = y + "-" + m + "-" + d;

export default {
  data: () => ({
    Model: "Vendor",
    endpoint: "vendor",
    currentDate,
    filters: {},
    options: {},
    loading: false,
    response: "",
    vendors: [],
    errors: [],
    headers: [
      {
        text: "Title",
        value: "title",
      },
      {
        text: "Full Name",
        value: "full_name",
      },
      {
        text: "Company",
        value: "company",
      },

      {
        text: "Contact",
        value: "contact",
      },
      {
        text: "Tax Registered",
        value: "tax_number",
      },
      {
        text: "Category",
        value: "vendor_category.name",
      },
      {
        text: "Action",
        align: "center",
        sortable: false,
        value: "options",
      },
    ],
    componentKey: 1,
  }),

  async created() {
    this.getDataFromApi();
    // this.getVendorCategory();
  },
  mounted() {},
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    getRandomId() {
      return ++this.componentKey;
    },
    async getVendorCategory() {
      let { data } = await this.$axios.get(this.endpoint);

      return data;
    },
    async getDataFromApi() {
      this.loading = true;
      let { data } = await this.$axios.get(this.endpoint);
      this.loading = false;

      this.vendors = data.data;
    },
  },
};
</script>
