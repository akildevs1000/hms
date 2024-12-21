<template>
  <v-card v-if="can(`posting_access`)">
    <h4>
      Postings : Total
      {{ $utils.currency_format(total) }}
    </h4>
    <v-row class="pt-3">
      <v-col style="max-width: 50px"
        ><v-icon color="primary" right class="mt-1" @click="getDataFromApi()"
          >mdi-reload</v-icon
        ></v-col
      ><v-col style="max-width: 180px">
        <v-text-field
          label="Search Bill No,Rev No"
          dense
          outlined
          clearable
          @click:clear="clear()"
          type="text"
          v-model="filterSearch"
          hide-details
        ></v-text-field>
      </v-col>
      <v-col style="max-width: 230px">
        <FilterDateRange
          v-if="date_from"
          :defaultDates="false"
          @filter-attr="filterAttr"
          :default_date_from="date_from"
          :default_date_to="date_to"
      /></v-col>
      <v-col>
        <div class="mx-2" style="margin-top: 1px">
          <v-btn
            small
            color="primary"
            @click="getDataFromApi"
            :loading="loading"
            >Submit</v-btn
          >
        </div>
      </v-col>
      <v-spacer></v-spacer>
    </v-row>
    <v-data-table
      dense
      :headers="headers"
      :items="data"
      :loading="loading"
      :options.sync="options"
      :server-items-length="totalRowsCount"
      :footer-props="{
        itemsPerPageOptions: [10, 20, 50, 100, 500, 1000],
      }"
      class="elevation-1 pa-3"
    >
      <template v-slot:item.sno="{ item, index }">
        <small class="text-color">
          {{
            currentPage
              ? (currentPage - 1) * perPage +
                (cumulativeIndex + data.indexOf(item))
              : "-"
          }}</small
        >
      </template>
      <template v-slot:[`item.bill_no`]="{ item }">
        <small class="text-color">{{ item.bill_no }}</small>
      </template>

      <template v-slot:[`item.booking.reservation_no`]="{ item }">
        <small class="text-color">{{ item.booking.reservation_no }}</small>
      </template>

      <template v-slot:[`item.posting_date`]="{ item }">
        <small class="text-color">{{ item.posting_date }}</small>
      </template>
      <template v-slot:[`item.booking.title`]="{ item }">
        <small class="text-color">{{ item.booking.title }}</small>
      </template>

      <template v-slot:[`item.booked_room.room_type`]="{ item }">
        <small class="text-color">{{ item.booked_room.room_type }}</small>
      </template>

      <template v-slot:[`item.booked_room.room_no`]="{ item }">
        <small class="text-color">{{ item.booked_room.room_no }}</small>
      </template>

      <template v-slot:[`item.tax_type`]="{ item }">
        <small class="text-color">{{ item.tax_type }}</small>
      </template>

      <template v-slot:[`item.item`]="{ item }">
        <small class="text-color">{{ item.item }}</small>
      </template>

      <template v-slot:[`item.qty`]="{ item }">
        <small class="text-color">{{ item.qty }}</small>
      </template>

      <template v-slot:[`item.single_amt`]="{ item }">
        <small class="text-color">{{ item.single_amt }}</small>
      </template>

      <template v-slot:[`item.amount`]="{ item }">
        <small class="text-color">{{ item.amount }}</small>
      </template>

      <template v-slot:[`item.tax`]="{ item }">
        <small class="text-color">{{ item.tax }}</small>
      </template>

      <template v-slot:[`item.amount_with_tax`]="{ item }">
        <small class="text-color">{{ item.amount_with_tax }}</small>
      </template>
    </v-data-table>
  </v-card>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    filterSearch: null,
    total: 0,
    page: 1,
    perPage: 0,
    currentPage: 1,
    cumulativeIndex: 1,

    Model: "Posting",
    endpoint: "posting",
    filters: {},
    options: { itemsPerPage: 20 },
    loading: false,
    response: "",
    date_from: null,
    date_to: "",
    data: [],
    errors: [],
    headers: [
      { text: "#", value: "sno", sortable: false, filterable: false },
      { text: "Bill No", value: "bill_no", sortable: false, filterable: false },
      {
        text: "Rev. No",
        value: "booking.reservation_no",
        sortable: false,
        filterable: false,
      },
      {
        text: "Date Time",
        value: "posting_date",
        sortable: false,
        filterable: false,
      },
      {
        text: "Name",
        value: "booking.title",
        sortable: false,
        filterable: false,
      },
      {
        text: "Room Type",
        value: "booked_room.room_type",
        sortable: false,
        filterable: false,
      },
      {
        text: "Room",
        value: "booked_room.room_no",
        sortable: false,
        filterable: false,
      },
      {
        text: "Category",
        value: "tax_type",
        sortable: false,
        filterable: false,
      },
      {
        text: "Item Description",
        value: "item",
        sortable: false,
        filterable: false,
      },
      { text: "Qty", value: "qty", sortable: false, filterable: false },
      {
        text: "Unit",
        value: "single_amt",
        align: `right`,
        sortable: false,
        filterable: false,
      },
      {
        text: "Sub Total",
        value: "amount",
        align: `right`,
        sortable: false,
        filterable: false,
      },
      {
        text: "Tax",
        value: "tax",
        align: `right`,
        sortable: false,
        filterable: false,
      },
      {
        text: "Total",
        value: "amount_with_tax",
        align: `right`,
        sortable: false,
        filterable: false,
      },
    ],
    componentKey: 1,
    totalRowsCount: 0,
  }),

  created() {
    let today = new Date();
    let monthObj = this.$dateFormat.monthStartEnd(today);
    console.log(monthObj);

    this.date_from = monthObj.first;
    this.date_to = monthObj.last;
    console.log(this.date_from);

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
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    filterAttr(data) {
      this.date_from = data.from;
      this.date_to = data.to;

      //this.getDataFromApi();
    },
    convert_date_format(val) {
      if (!val) return "---";
      const date = new Date(val);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return [year, month, day].join("-");
    },
    clear() {
      this.filterSearch = null;
      this.getDataFromApi();
    },
    async getDataFromApi() {
      //let page = this.pagination.current;
      this.currentPage = this.currentPage ?? 1;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      this.perPage = itemsPerPage;
      // if (!page > 0) return false;
      this.loading = true;
      let options = {
        params: {
          page: page,
          //sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          pagination: true,
          company_id: this.$auth.user.company.id,
          date_from: this.date_from,
          date_to: this.date_to,
          search: this.filterSearch,
        },
      };

      this.$axios.get(this.endpoint, options).then(({ data }) => {
        this.currentPage = page;
        this.data = data.data;

        this.loading = false;
        this.totalRowsCount = data.total;

        this.getGrandTotalAmount();
      });
    },

    getGrandTotalAmount() {
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          date_from: this.date_from,
          date_to: this.date_to,
          search: this.filterSearch,
        },
      };

      this.$axios.get("posting_total", options).then(({ data }) => {
        this.total = data.total;
      });
    },
  },
};
</script>
