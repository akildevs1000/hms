<template>
  <v-data-table
    dense
    :headers="headers"
    :items="expenses"
    :loading="loading"
    :options.sync="options"
    :footer-props="{
      itemsPerPageOptions: [100, 500, 1000],
    }"
  >
    <template v-slot:top>
      <v-toolbar flat dense class="mb-5">
        {{ Model }}
        <v-icon color="primary" right @click="getDataFromApi()"
          >mdi-reload</v-icon
        >
        <v-spacer></v-spacer>
        <QuotationRoomCreate
          :key="QuotationRoomCreateCompKey"
          :model="Model"
          :endpoint="endpoint"
          @response="getDataFromApi"
        />
      </v-toolbar>
    </template>
    <template v-slot:item.customer="{ item }">
      {{ item?.customer?.first_name }} {{ item?.customer?.last_name }}
    </template>

    <template v-slot:item.followups="{ item }">
      <QuotationFollowupPopup
        v-if="item.followups.length"
        :followups="item.followups"
      />
    </template>

    <template v-slot:item.status="{ item }">
      {{ item.status || "Open" }}
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
              <v-icon small color="blue" @click="openExternalWinodw(item.id)">mdi-eye</v-icon>
              View
              <!-- <QuotationRoomView
                :model="Model"
                :endpoint="endpoint"
                :item="item"
                @response="getDataFromApi"
              /> -->
            </v-list-item-title>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>
              <QuotationFollowup
                :model="Model"
                :endpoint="`followup`"
                :id="item.id"
                @response="getDataFromApi"
              />
            </v-list-item-title>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>
              <QuotationRoomEdit
                :model="Model"
                :endpoint="endpoint"
                :item="item"
                @response="getDataFromApi"
              />
            </v-list-item-title>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>
              <QuotationRoomClone
                :model="Model"
                :endpoint="endpoint"
                :item="item"
                @response="getDataFromApi"
              />
            </v-list-item-title>
          </v-list-item>
          <!-- <v-list-item>
            <v-list-item-title>
              <QuotationConvertToInvoice
                :model="Model"
                :endpoint="`invoice-v1`"
                :item="item"
                @response="getDataFromApi"
              />
            </v-list-item-title>
          </v-list-item> -->
          <v-list-item>
            <v-list-item-title>
              <QuotationDelete
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
</template>

<script>
let date = new Date();

let d = date.getDate();
let m = (date.getMonth() + 1).toString().padStart(2, "0");
let y = date.getFullYear();
let currentDate = y + "-" + m + "-" + d;

export default {
  data: () => ({
    Model: "Quotation",
    endpoint: "quotation",
    QuotationRoomCreateCompKey:1,
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
        value: "ref_no",
      },
      {
        text: "Customer",
        value: "customer",
      },
      {
        text: "Quotation Date",
        value: "book_date",
      },
      // {
      //   text: "Arrival Date",
      //   value: "arrival_date",
      // },
      // {
      //   text: "Departure Date",
      //   value: "departure_date",
      // },
      {
        text: "Total",
        value: "total",
      },
      {
        text: "Status",
        value: "status.status",
      },
      {
        text: "Follow Up",
        value: "followups",
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
    openExternalWinodw(id) {
      let url = `${process.env.BACKEND_URL}quotation-room/${id}`;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", url);
      document.body.appendChild(element);
      element.click();
    },
    getRandomeId() {
      return Math.random();
    },
    async getDataFromApi() {
      this.QuotationRoomCreateCompKey +=1;
      this.loading = true;
      let config = {
        params: { type: "room", company_id: this.$auth.user.company_id },
      };
      let { data } = await this.$axios.get(this.endpoint, config);
      this.loading = false;
      this.expenses = data.data;
    },
  },
};
</script>
