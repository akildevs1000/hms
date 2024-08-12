<template>
  <v-card style="background: none">
    <v-data-table
      dense
      :headers="headers"
      :items="expenses"
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
          <ExpenseManagementCreate
            :model="Model"
            :endpoint="endpoint"
            @response="getDataFromApi"
          />
        </v-toolbar>
      </template>
      <template v-slot:item.attachments="{ item }">
        <div v-if="item.attachments">
          <ViewMultipleAttachments
            :key="getRandomeId()"
            :attachments="item.attachments"
          />
        </div>
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
                <ExpenseManagementView
                  :model="Model"
                  :endpoint="endpoint"
                  :item="item"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <ExpenseManagementEdit
                  :model="Model"
                  :endpoint="endpoint"
                  :item="item"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <ExpenseManagementPayment
                  :endpoint="`expense-payment`"
                  :id="item.id"
                  :vendor_id="item.vendor_id"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
            <v-list-item @click="openVoucher(item.id)">
              <v-list-item-title>
                <v-icon color="blue" small> mdi-cash-multiple </v-icon>
                Voucher
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <ExpenseManagementDelete
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
</template>

<script>
let date = new Date();

let d = date.getDate();
let m = (date.getMonth() + 1).toString().padStart(2, "0");
let y = date.getFullYear();
let currentDate = y + "-" + m + "-" + d;

export default {
  data: () => ({
    Model: "Expense",
    endpoint: "admin-expense",
    currentDate,
    filters: {},
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
        text: "Vendor",
        value: "vendor.first_name",
      },
      {
        text: "Total",
        value: "total",
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
        text: "Status",
        value: "status",
      },
      {
        text: "Attachments",
        value: "attachments",
      },
      {
        text: "Created At",
        value: "created_at",
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
    openVoucher(id) {
      window.open(
        `https://hms-backend.test/api/payment-voucher/${id}?payee=${this.$auth.user.name}`
      );
    },
    getRandomeId() {
      return Math.random();
    },
    async getDataFromApi() {
      this.loading = true;
      let config = {
        params: {
          is_admin_expense: 1,
        },
      };
      let { data } = await this.$axios.get(this.endpoint,config);
      this.loading = false;
      this.expenses = data.data;
    },
  },
};
</script>
