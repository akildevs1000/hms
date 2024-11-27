<template>
  <div v-if="can(`night_audit_access`)">
    <v-card class="pa-5">
      <v-row>
        <v-col cols="10"></v-col>
        <v-col cols="2">
          <FilterDateRange @filter-attr="filterAttr" />
        </v-col>
        <v-col md="12" class="text-right">
          <AssetsTable height="500" :headers="headers" :items="items">
            <template #row>
              <tr v-if="summaryRow">
                <td class="text-center black--text py-1">Total</td>
                <td
                  class="text-center black--text py-1"
                  v-for="(row, index) in summaryRow"
                  :key="index"
                >
                  {{ row }}
                </td>
              </tr>
            </template>
          </AssetsTable>
        </v-col>
      </v-row>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
let date = new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
  .toISOString()
  .substr(0, 10);

export default {
  data: () => ({
    stats: [],
    from_date: date,
    to_date: date,
    from_menu: false,
    options: {},
    search: "",
    snackbar: false,
    dialog: false,
    loading: true,
    payloadOptions: {
      from_date: date,
      to_date: date,
    },
    items: [],
    headers: [],
    summaryRow: null,
  }),

  created() {
    this.getdata();
  },

  methods: {
    filterAttr(data) {
      this.payloadOptions = {
        from_date: data.from,
        to_date: data.to,
      };

      this.getdata();
    },
    openExternalLink(path) {
      let url = `https://backend.myhotel2cloud.com/api/get_audit_report_print?path=${path}`;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", url);
      document.body.appendChild(element);
      console.log(element);
      element.click();
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    getSum(item, type) {
      let sum = 0;
      item.map((e) => {
        e.transactions.map((e) =>
          e.payment_method_id == type ? (sum += parseFloat(e.credit)) : 0
        );
      });
      return sum.toFixed(2);
    },

    async getdata() {
      this.loading = true;
      const url = "cash-report";
      this.payloadOptions.company_id = this.$auth.user.company_id;

      try {
        const response = await this.$axios.get(url, {
          params: { ...this.payloadOptions },
        });

        const { data } = response;
        // Update headers and items
        this.headers = data.headers || [];
        this.items =
          data?.data?.map((e) => {
            return {
              id: e.id,
              date: e.date,
              sold: e.data.displayValues.sold,
              cash: e.data.displayValues.cash,
              expense: e.data.displayValues.expense,
              balance: e.data.displayValues.balance,
            };
          }) || [];

        this.summaryRow = data.summaryRow;

        this.summaryRow.balance = this.$utils.currency_format(
          data?.data?.reduce((acc, cur) => acc + cur.data.balance, 0)
        );
      } catch (error) {
        console.error("Error fetching data:", error);
        // Optionally show an error message to the user
      } finally {
        this.loading = false; // Ensure loading state is updated
      }
    },
  },
};
</script>
