<template>
  <v-container>
    <v-row v-if="toggleShortView">
      <v-col cols="3">
        <v-card class="mx-2">
          <div class="d-flex pa-2 justify-space-between">
            <div>
              View Expense
              <v-icon color="secondary" @click="getDataFromApi()"
                >mdi-reload</v-icon
              >
            </div>
            <ExpenseCreate
              :model="Model"
              :endpoint="endpoint"
              @response="getDataFromApi"
              @close="refreshKey"
            />
          </div>
          <v-data-table
            class="pa-1"
            dense
            :headers="shortViewHeaders"
            :items="expenses"
            :server-items-length="totalRowsCount"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
          >
            <template v-slot:top>
              <v-container>
                <v-row>
                  <v-col cols="6" v-if="can(`management_expense_access`)">
                    <v-autocomplete
                      label="Select Expense Type"
                      dense
                      outlined
                      v-model="filters.is_admin_expense"
                      :items="[
                        { id: 1, name: `Manager Expense` },
                        { id: 0, name: `Non Manager Expense` },
                      ]"
                      item-text="name"
                      item-value="id"
                      :hide-details="true"
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="6">
                    <v-autocomplete
                      label="Select Category"
                      dense
                      outlined
                      v-model="filters.vendor_category_id"
                      :items="[
                        { id: 0, name: `Select All` },
                        ...vendor_categories,
                      ]"
                      item-value="id"
                      item-text="name"
                      :hide-details="true"
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="6">
                    <v-autocomplete
                      label="Select Vendor"
                      dense
                      outlined
                      v-model="filters.vendor_id"
                      :items="[{ id: 0, first_name: `Select All` }, ...vendors]"
                      item-value="id"
                      item-text="first_name"
                      :hide-details="true"
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="6">
                    <FilterDateRange
                      :defaultDates="true"
                      @filter-attr="filterAttr"
                    />
                  </v-col>
                  <v-col cols="12">
                    <v-btn
                      small
                      color="primary"
                      @click="getDataFromApi"
                      :loading="loading"
                      >Submit</v-btn
                    >
                  </v-col>
                </v-row>
              </v-container>
            </template>
            <template v-slot:item.is_admin_expense="{ item }">
              {{
                item.is_admin_expense ? "Management Expense" : "Non Management"
              }}
            </template>
            <template v-slot:item.attachments="{ item }">
              <div v-if="item.attachments.length > 0">
                <ViewMultipleAttachments
                  :key="getRandomeId()"
                  :attachments="item.attachments"
                />
              </div>
            </template>
            <template v-slot:item.id="{ item }">
              <span
                @click="
                  () => {
                    selectedItem = item;
                  }
                "
                class="blue--text"
                >{{ item.id }}</span
              >
            </template>
            <template v-slot:item.category="{ item }">
              {{ item?.vendor?.vendor_category?.name }}
            </template>
            <template v-slot:item.vendor="{ item }">
              {{ item.vendor.first_name }}
            </template>

            <template v-slot:item.sub_total="{ item }">
              {{ $utils.currency_format(item.sub_total) }}
            </template>
            <template v-slot:item.tax="{ item }">
              {{ $utils.currency_format(item.tax) }}
            </template>
            <template v-slot:item.total="{ item }">
              {{ $utils.currency_format(item.total) }}
            </template>
          </v-data-table>
        </v-card>
      </v-col>
      <v-col cols="9">
        <ExpenseViewPage
          v-if="selectedItem && selectedItem.id"
          :key="selectedItem.id"
          :model="Model"
          :endpoint="endpoint"
          :item="selectedItem"
          @response="getDataFromApi"
        />
      </v-col>
    </v-row>

    <v-card v-else style="background: none" elevation="0">
      <v-data-table
        dense
        :headers="headers"
        :items="expenses"
        :server-items-length="totalRowsCount"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [10, 50, 100, 500, 1000],
        }"
        class="elevation-1 pa-3"
      >
        <template v-slot:top>
          <v-toolbar flat dense class="mb-2">
            <v-icon color="primary" right class="mt-1" @click="getDataFromApi()"
              >mdi-reload</v-icon
            >
            <v-spacer></v-spacer>

            <!-- <v-btn
            @click="
              () => {
                $router.push(`/expense/create`);
              }
            "
            small
            color="primary"
            class="white--text"
            dark
          >
            <v-icon color="white" small> mdi-plus </v-icon> New
          </v-btn> -->

            <ExpenseCreate
              :model="Model"
              :endpoint="endpoint"
              @response="getDataFromApi"
              @close="refreshKey"
            />
          </v-toolbar>

          <div class="d-flex pb-2 px-4">
            <div class="mr-2" style="margin-top: 1px">
              <v-autocomplete
                label="Select Category"
                dense
                outlined
                v-model="filters.vendor_category_id"
                :items="[{ id: 0, name: `Select All` }, ...vendor_categories]"
                item-value="id"
                item-text="name"
                :hide-details="true"
              ></v-autocomplete>
            </div>
            <div class="mr-2" style="margin-top: 1px">
              <v-autocomplete
                label="Select Vendor"
                dense
                outlined
                v-model="filters.vendor_id"
                :items="[{ id: 0, first_name: `Select All` }, ...vendors]"
                item-value="id"
                item-text="first_name"
                :hide-details="true"
              ></v-autocomplete>
            </div>
            <FilterDateRange :defaultDates="true" @filter-attr="filterAttr" />

            <div class="mx-2" style="margin-top: 1px">
              <v-btn
                small
                color="primary"
                @click="getDataFromApi"
                :loading="loading"
                >Submit</v-btn
              >
            </div>

            <!-- Buttons aligned to the right -->
            <div class="d-flex ml-auto" style="margin-top: 1px">
              <AssetsIcon
                icon="printer-outline"
                @click="handleLink(`admin-expense-print`)"
              />
              &nbsp;
              <AssetsIcon
                icon="download-outline"
                @click="handleLink(`admin-expense-download`)"
              />
            </div>
          </div>
        </template>
        <template v-slot:item.is_admin_expense="{ item }">
          {{ item.is_admin_expense ? "Management Expense" : "Non Management" }}
        </template>
        <template v-slot:item.attachments="{ item }">
          <div v-if="item.attachments.length > 0">
            <ViewMultipleAttachments
              :key="getRandomeId()"
              :attachments="item.attachments"
            />
          </div>
        </template>
        <template v-slot:item.id="{ item }">
          <span
            @click="
              () => {
                toggleShortView = !toggleShortView;
                selectedItem = item;
              }
            "
            class="blue--text"
            >{{ item.id }}</span
          >
        </template>
        <template v-slot:item.category="{ item }">
          {{ item?.vendor?.vendor_category?.name }}
        </template>
        <template v-slot:item.vendor="{ item }">
          {{ item.vendor.first_name }}
        </template>

        <template v-slot:item.sub_total="{ item }">
          {{ $utils.currency_format(item.sub_total) }}
        </template>
        <template v-slot:item.tax="{ item }">
          {{ $utils.currency_format(item.tax) }}
        </template>
        <template v-slot:item.total="{ item }">
          {{ $utils.currency_format(item.total) }}
        </template>
        <template v-slot:item.options="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>

            <v-list width="120" dense>
              <v-list-item>
                <v-list-item-title>
                  <ExpenseView
                    :model="Model"
                    :endpoint="endpoint"
                    :item="item"
                    @response="getDataFromApi"
                  />
                </v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>
                  <ExpenseEdit
                    :model="Model"
                    :endpoint="endpoint"
                    :item="item"
                    @response="
                      () => {
                        ExpensePaymentKey++;
                        getDataFromApi();
                      }
                    "
                    :key="ExpensePaymentKey + 1"
                  />
                </v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>
                  <ExpensePayment
                    :model="Model"
                    :endpoint="endpoint"
                    :item="item"
                    @response="
                      () => {
                        ExpensePaymentKey++;
                        getDataFromApi();
                      }
                    "
                    :key="ExpensePaymentKey + 2"
                  />
                </v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>
                  <ExpenseDelete
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
    </v-card>
  </v-container>
</template>

<script>
let date = new Date();

let d = date.getDate();
let m = (date.getMonth() + 1).toString().padStart(2, "0");
let y = date.getFullYear();
let currentDate = y + "-" + m + "-" + d;

export default {
  data: () => ({
    selectedItem: null,
    totalRowsCount: 0,
    ExpensePaymentKey: 1,
    Model: "Expense",
    endpoint: "admin-expense",
    currentDate,
    options: {},
    loading: false,
    response: "",
    expenses: [],
    errors: [],
    headers: [
      {
        text: "Ref #",
        value: "id",
      },
      {
        text: "Expense Type",
        value: "is_admin_expense",
      },
      {
        text: "Category",
        value: "category",
      },
      {
        text: "Vendor",
        value: "vendor",
      },
      {
        text: "Bill #",
        value: "bill_number",
      },
      {
        text: "Bill Date",
        value: "bill_date",
      },
      {
        text: "Sub Total",
        value: "sub_total",
      },
      {
        text: "Tax",
        value: "tax",
      },
      {
        text: "Total",
        value: "total",
      },
      {
        text: "Attachments",
        value: "attachments",
      },
      {
        text: "Status",
        value: "status",
      },
      {
        text: "Action",
        align: "center",
        sortable: false,
        value: "options",
      },
    ],
    shortViewHeaders: [
      {
        text: "Ref #",
        value: "id",
      },
      {
        text: "Expense Type",
        value: "is_admin_expense",
      },
      {
        text: "Category",
        value: "category",
      },
    ],
    componentKey: 1,

    filters: {
      from: new Date().toJSON().slice(0, 10),
      to: new Date().toJSON().slice(0, 10),
      vendor_category_id: 0,
      vendor_id: 0,
    },

    vendor_categories: [],
    vendors: [],
    dialogKey: 1,
    toggleShortView: false,
  }),

  async created() {
    this.getDataFromApi();
    this.getVendorCategory();
    this.getVendors();
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
    can(per) {
      let user = this.$auth.user;
      if (user.is_master) {
        return true;
      }
      return user && user.permissions.some((e) => e == per || per == "/");
    },
    refreshKey() {
      this.dialogKey++;
    },
    handleLink(endpoint) {
      this.filters = {
        ...this.filters,
        company_id: this.$auth.user.company_id,
      };

      // if (process.env.LOCAL_IP == "localhost") {
      //   endpoint = `https://hms-backend.test/api/` + endpoint;
      // }

      this.$utils.open_external_link(
        `${process.env.BACKEND_URL}${endpoint}?${this.buildQueryParams(
          this.filters
        )}`
      );
    },
    buildQueryParams(params) {
      return Object.keys(params)
        .map(
          (key) =>
            `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`
        )
        .join("&");
    },
    async getVendorCategory() {
      let { data } = await this.$axios.get(`vendor-category-list`);
      this.vendor_categories = data;
    },
    async getVendors() {
      let { data } = await this.$axios.get(`vendor-list`);
      this.vendors = data;
    },
    async filterAttr(data) {
      this.filters = {
        ...this.filters,
        from: data.from,
        to: data.to,
      };
    },
    getRandomeId() {
      return Math.random();
    },
    async getDataFromApi() {
      this.loading = true;
      let { sortDesc, page, itemsPerPage } = this.options;

      let sortedDesc = sortDesc ? sortDesc[0] : "";
      this.perPage = itemsPerPage;
      this.currentPage = page;
      if (!page > 0) return false;

      if (!this.can(`management_expense_access`)) {
        this.filters.is_admin_expense = 0;
      }

      let config = {
        params: {
          ...this.filters,
          page: page,
          //sortBy: sortedBy,
          sortDesc: sortedDesc,
          perPage: itemsPerPage,
          pagination: true,
        },
      };
      let { data } = await this.$axios.get(this.endpoint, config);
      this.loading = false;
      this.expenses = data.data;

      this.totalRowsCount = data.total;
    },
  },
};
</script>
