<template>
  <v-card class="mt-2">
    <v-container fluid>
      <div class="pa-3">
        <div style="display: flex; justify-content: right">
          <FilterDateRange :defaultDates="true" @filter-attr="filterAttr" />
        </div>
      </div>
      <div :style="`max-height: 600px; overflow: auto`" class="px-1">
        <table cellspacing="0" style="width: 100%">
          <thead>
            <tr>
              <td
                v-for="(col, index) in headers"
                :key="index"
                class="primary--text py-1 px-2 border-top border-bottom"
                :class="`text-${col.align}`"
                width="30px"
              >
                {{ col.text }}
              </td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in data" :key="index">
              <td
                v-for="(header, tdIndex) in headers"
                :key="tdIndex"
                :class="`text-${header.align}`"
                class="py-1 px-2 border-bottom text-color"
              >
                <div v-if="header.value == 'date'">
                  {{ item[header.value] }}
                </div>
                <div v-else>
                  {{ item.data[header.value]?.count }}
                  <br />
                  {{ item.data[header.value]?.total_sum }}
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </v-container>
  </v-card>
</template>
<script>
export default {
  data: () => ({
    endpoint: "ota-trn",
    search: null,
    filter: null,
    stats: [],
    cumulativeIndex: 1,
    perPage: 20,
    currentPage: 1,
    filterLoader: false,
    isFilter: false,
    totalRowsCount: 0,
    options: {},
    // Model: "In House Reservation",
    payingDialog: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 30,
    },
    guest_mode: "",

    from_date: "",
    from_menu: false,

    to_date: "",
    to_menu: false,

    type: "",
    source: "",
    agentList: [],
    types: ["Select All", "Online", "Travel Agency", "Walking"],
    sources: [],

    options: {},
    // endpoint: "in_house_reservation_list",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,

    headers: [],
    editedIndex: -1,
    response: "",
    errors: [],
    checkData: {},
    new_payment: 0,
  }),

  computed: {},

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    this.getDataFromApi();
  },

  methods: {
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filter = {
        from_date: data.from,
        to_date: data.to,
      };
      this.getDataFromApi();
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    convert_date_format(val) {
      const date = new Date(val);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");

      return [year, month, day].join("-");
    },
    getDataFromApi(url = this.endpoint, customPage = 0) {
      if (this.from_date && this.to_date) {
        this.loading = true;

        let { sortBy, sortDesc, page, itemsPerPage } = this.options;

        let sortedBy = sortBy ? sortBy[0] : "";
        let sortedDesc = sortDesc ? sortDesc[0] : "";
        if (customPage == 1) page = 1;
        this.currentPage = page;
        let options = {
          params: {
            page: page,
            sortBy: sortedBy,
            sortDesc: sortedDesc,
            per_page: itemsPerPage,
            company_id: this.$auth.user.company.id,
            ...this.filter,
          },
        };
        this.$axios.get(url, options).then(({ data }) => {
          this.data = data.data;
          this.headers = data.headers;
          this.pagination.current = data.current_page;
          this.pagination.total = data.last_page;
          this.loading = false;
          this.totalRowsCount = data.total;
          this.currentPage = page;
          this.perPage = itemsPerPage;
        });
      }
    },
  },
};
</script>
