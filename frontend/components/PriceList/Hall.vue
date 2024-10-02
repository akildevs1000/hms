<template>
  <div
    v-if="can('settings_room_price_access') && can('settings_room_price_view')"
  >
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog v-model="priceEditDialog" max-width="480px">
      <v-card>
        <v-toolbar flat dense>
          Edit <v-spacer></v-spacer>
          <v-icon @click="priceEditDialog = false">mdi-close</v-icon>
        </v-toolbar>

        <v-divider></v-divider>
        <h4
          style="
            text-transform: capitalize;
            margin: 11px 20px -25px 20px;
            color: #aaaaaa;
          "
        >
          {{ editPriceList.name }}
        </h4>
        <v-card-text class="mt-8">
          <v-row>
            <v-col md="4">
              <v-text-field
                type="number"
                v-model.number="editPriceList.weekday_price"
                label="Weekdays Amount"
                placeholder="Weekdays Amount"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>
            <v-col md="4">
              <v-text-field
                type="number"
                v-model.number="editPriceList.weekend_price"
                label="	Weekend Amount"
                placeholder="Weekend Amount"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>
            <v-col md="4">
              <v-text-field
                type="number"
                v-model.number="editPriceList.holiday_price"
                label="Holiday Amount"
                placeholder="Holiday Amount"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>
            <v-col md="12">
              <v-text-field
                type="number"
                v-model.number="editPriceList.hall_min_hours"
                label="Hall Min Hours"
                placeholder="Hall Min Hours"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>

            <v-col md="12">
              <v-text-field
                type="number"
                v-model.number="editPriceList.extra_hours_charges"
                label="Extra Hour Charges"
                placeholder="Extra Hour Charges"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>
            
            <v-col md="12">
              <v-text-field
                type="number"
                v-model.number="editPriceList.electricity_charges"
                label="Electricity Charges"
                placeholder="Electricity Charges"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>

            <v-col md="12">
              <v-text-field
                type="number"
                v-model.number="editPriceList.generator_charges"
                label="Generator Charges"
                placeholder="Generator Charges"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>

            <v-col md="12">
              <v-text-field
                type="number"
                v-model.number="editPriceList.cleaning_charges"
                label="Cleaning Charges"
                placeholder="Cleaning Charges"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>

            <v-col md="12">
              <v-text-field
                type="number"
                v-model.number="editPriceList.audio_charges"
                label="Audio Charges"
                placeholder="Audio Charges"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>

            <v-col md="12">
              <v-text-field
                type="number"
                v-model.number="editPriceList.projector_charges"
                label="Projector Charges"
                placeholder="Projector Charges"
                id="id"
                outlined
                dense
                hide-details
              >
              </v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" class="text-right">
              <v-btn
                color="grey white--text"
                small
                @click="priceEditDialog = false"
              >
                Close
              </v-btn>
              <v-btn class="primary" small @click="update_price">
                Update
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-card class="mb-5 rounded-md mt-3 px-2" elevation="0">
      <v-data-table
        :headers="headers"
        :items="priceList"
        :loading="loading"
        hide-default-footer
      >
        <template v-slot:item.index="{ index }">
          {{ index + 1 }}
        </template>

        <template v-slot:item.name="{ item }">
          <span style="text-transform: uppercase">{{ item.name }}</span>
        </template>

        <template v-slot:item.action="{ item }">
          <v-menu bottom left v-if="can('settings_room_price_edit')">
            <template v-slot:activator="{ on, attrs }">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <v-list width="120" dense>
              <v-list-item
                v-if="can('settings_room_price_edit')"
                @click="priceEditItem(item)"
              >
                <v-list-item-title style="cursor: pointer">
                  <v-icon color="secondary" small>mdi-pencil</v-icon>
                  Edit
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table>
    </v-card>
  </div>

  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    additionalChargesError: null,
    additionalCharges: {
      early_check_in: 0,
      late_check_out: 0,
    },
    tab: null,
    // items: ["Weekdays", "Weekend", "Holidays", "Prices"],
    items: ["Prices", "Weekend", "Holidays", "Additional Charges", "Food Plan"],
    Model: "Holidays Price",
    selectedWeekDays: [],
    dates: [],
    description: "",
    numberOfDays: "",
    from_date_menu: false,
    from_date: "",
    to_date: "",
    to_date_menu: false,

    holidayOptions: {},
    filters: {},
    isFilter: false,
    totalUserData: 0,
    holidayHeaders: [
      { text: "Name", value: "description", filterable: true },
      { text: "From", value: "from", filterable: false },
      { text: "to", value: "to", filterable: false },
      { text: "Actions", value: "actions", filterable: false, sortable: false },
    ],

    Weekdays: [
      { name: "Mon" },
      { name: "Tue" },
      { name: "Wed" },
      { name: "Thu" },
      { name: "Fri" },
      { name: "Sat" },
      { name: "Sun" },
    ],

    pagination: {
      current: 1,
      total: 0,
      per_page: 17,
      status: "-1",
    },
    editPriceList: {
      weekday_price: 0,
      weekend_price: 0,
      holiday_price: 0,

      hall_min_hours: 4,
      projector_charges: 0,
      extra_hours_charges: 0,
      cleaning_charges: 0,
      electricity_charges: 0,
      generator_charges: 0,
      audio_charges: 0,
      company_id: 0,
    },

    editWeekend: {
      name: [],
    },

    weekendId: "",

    options: {},
    endpoint: "holiday",
    search: "",
    holidayDialog: false,
    priceEditDialog: false,
    weekendDialog: false,
    isUpdate: false,
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    id: "",
    priceId: "",
    headers: [
      { text: "#", value: "index", sortable: false },
      { text: "Room Type", value: "name" },
      { text: "Weekdays Amount", value: "weekday_price" },
      { text: "Weekend Amount", value: "weekend_price" },
      { text: "Holiday Amount", value: "holiday_price" },
      { text: "Action", value: "action", sortable: false },
    ],
    weekendHeaders: [
      { text: "#", value: "index", sortable: false },
      { text: "Weekend", value: "day" },
      { text: "Action", value: "action", sortable: false },
    ],
    // headers: [
    //   { text: "#" },
    //   { text: "From" },
    //   { text: "To" },
    //   { text: "Desc" },
    //   { text: "Action" },
    // ],
    editedIndex: -1,
    response: "",
    priceList: [],
    weekendList: [],
    errors: [],
  }),

  watch: {
    holidayDialog(val) {
      !val ? this.close() : "";
      console.log(val);
    },

    from_date() {
      this.getDays();
    },

    to_date() {
      this.getDays();
    },

    holidayOptions: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },

  created() {
    this.loading = true;
    this.get_price_list();
    this.get_weekends();
    this.getAdditionalCharges();
  },

  computed: {
    dateRangeText() {
      return this.dates.join(" ~ ");
    },
    formTitle() {
      return this.isUpdate ? "Update" : "Create";
    },
  },

  methods: {
    async getAdditionalCharges() {
      try {
        let { data } = await this.$axios.get(`additional_charges`, {
          params: {
            company_id: this.$auth.user.company_id,
          },
        });

        this.additionalCharges = data;
      } catch (error) {
        this.additionalChargesError = error;
      }
    },

    async submitAdditionalCharges() {
      try {
        this.additionalCharges.company_id = this.$auth.user.company_id;
        await this.$axios.post(`additional_charges`, this.additionalCharges);
        alert(`Additional Charges has been added`);
      } catch (error) {
        this.additionalChargesError = error;
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    onPageChange() {
      this.getDataFromApi();
    },

    toggleFilter() {
      this.isFilter = !this.isFilter;
      console.log(this.isFilter);
    },

    close() {
      this.description = "";
      this.from_date = "";
      this.to_date = "";
      this.numberOfDays = "";
    },

    editItem(item) {
      this.holidayDialog = true;
      // console.log(item);
      this.id = item.id;

      this.description = item.description;
      this.isUpdate = true;
      this.dates = [item.from, item.to];

      this.from_date = item.from;
      this.to_date = item.to;
    },

    priceEditItem(item) {
      this.priceId = item.id;
      this.editPriceList = item;
      this.priceEditDialog = true;
    },

    weekendEditItem(item) {
      this.weekendId = item.id;
      this.editWeekend.name = item.day;
      this.weekendDialog = true;
    },

    clear() {
      this.isUpdate = false;
      this.description = "";
      this.dates = [];
    },

    deleteItem(item) {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`holiday` + "/" + item.id, payload)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    getDataFromApi(url = this.endpoint) {
      const { sortBy, sortDesc, page, itemsPerPage } = this.holidayOptions;
      let sortedBy = sortBy[0];
      let sortedDesc = sortDesc[0];
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          page: page,
          itemsPerPage: itemsPerPage,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          ...this.filters,
        },
      };
      console.log(options);
      this.$axios.get(url, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.totalUserData = data.data.total;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    get_price_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          type: "hall",
        },
      };
      this.$axios.get("get_price_list", payload).then(({ data }) => {
        this.priceList = data;
      });
    },

    get_weekends() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get("weekend", payload).then(({ data }) => {
        this.loading = false;
        this.weekendList = data;
      });
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi(this.endpoint);
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    applyFilters() {
      this.getDataFromApi();
    },

    update_holidays() {
      let payload = {
        // dates: this.dates,
        dates: [this.from_date, this.to_date],
        description: this.description,
        company_id: this.$auth.user.company.id,
      };

      this.$axios
        .put(`holiday/${this.id}`, payload)
        .then(({ data }) => {
          console.log(data);
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.holidayDialog = false;
            this.getDataFromApi();
            this.snackbar = true;
            this.response = "Holiday successfully updated";
            this.description = "";
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
    },

    update_weekend() {
      let payload = {
        day: this.editWeekend.name,
        company_id: this.$auth.user.company.id,
      };
      this.$axios
        .put(`weekend/${this.weekendId}`, payload)
        .then(({ data }) => {
          console.log(data);
          if (!data.status) {
            this.errors = data.errors;
          } else {
            console.log(data.status);
            this.get_weekends();
            this.weekendDialog = false;
            this.snackbar = true;
            this.response = "Weekend successfully updated";
            this.description = "";
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
    },

    update_price() {
      let payload = {
        company_id: this.$auth.user.company.id,

        weekday_price: this.editPriceList.weekday_price,
        weekend_price: this.editPriceList.weekend_price,
        holiday_price: this.editPriceList.holiday_price,
        hall_min_hours: this.editPriceList.hall_min_hours,

        cleaning_charges: this.editPriceList.cleaning_charges,
        electricity_charges: this.editPriceList.electricity_charges,
        audio_charges: this.editPriceList.audio_charges,
        projector_charges: this.editPriceList.projector_charges,

        generator_charges: this.editPriceList.generator_charges,
        extra_hours_charges: this.editPriceList.extra_hours_charges,
      };
      this.$axios
        .put(`update_room_price/${this.priceId}`, payload)
        .then(({ data }) => {
          console.log(data);
          if (!data.status) {
            this.errors = data.errors;
          } else {
            console.log(data.status);
            // this.get_price_list();
            this.priceEditDialog = false;
            this.snackbar = true;
            this.response = "Price successfully updated";
            this.editPriceList = {};
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
      // this.updatePriceToWordpress();
    },
    updatePriceToWordpress() {
      //console.log(this.$auth.user.company);
      window.open(
        this.$auth.user.company.wordpress_push_prices_url,
        "blank",
        "noreferrer"
      );
      //update price to wordpress
      this.$axios
        .get(this.$auth.user.company.wordpress_push_prices_url, null)
        .then(({ data }) => {
          console.log(data);
        })
        .catch((res) => console.log(res));
    },
    getDays() {
      let f_date = new Date(this.from_date);
      let t_date = new Date(this.to_date);
      let Difference_In_Time = t_date.getTime() - f_date.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24) + 1;
      if (days > 0) {
        this.numberOfDays = days;
      }
    },

    store_holidays() {
      let payload = {
        // dates: this.dates,
        dates: [this.from_date, this.to_date],
        description: this.description,
        company_id: this.$auth.user.company.id,
      };
      this.$axios
        .post(this.endpoint, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            console.log(data.status);
            this.getDataFromApi();
            this.holidayDialog = false;
            this.snackbar = true;
            this.response = "Holiday successfully added";
            this.description = "";
            this.dates = [];
            this.errors = [];
            this.search = "";
          }
        })
        .catch((res) => console.log(res));
    },
  },
};
</script>