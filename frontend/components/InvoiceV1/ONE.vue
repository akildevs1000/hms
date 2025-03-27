<template>
    <v-row no-gutters>
      <v-col cols="3" style="max-width: 350px">
        <v-container>
          <v-toolbar flat dense>
            <v-icon @click="getDataFromApi()" color="primary">mdi-reload</v-icon>
            <v-spacer></v-spacer>
            <span class="subtitle-1">{{ Model }}s</span>
            <!-- Reduced font size here -->
          </v-toolbar>
          <v-data-table
            dense
            :headers="headers"
            :items="expenses"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [100, 500, 1000],
            }"
            hide-default-header
            hide-default-footer
          >
            <template v-slot:item.customer="{ item }">
              <v-row
                @click="selectedItem = item"
                class="d-flex align-center py-2"
              >
                <!-- Customer Info with Smaller Font Sizes -->
                <v-col cols="12" md="6">
                  <div>
                    <b
                      >{{ item?.customer?.first_name || "---" }}
                      {{ item?.customer?.last_name || "---" }}</b
                    >
                  </div>
                  <div>
                    <small>
                      {{ item?.ref_no || "---" }} -
                      {{ item?.created_at || "---" }}
                    </small>
                  </div>
                </v-col>
  
                <!-- Total and Status with Smaller Font Sizes -->
                <v-col cols="12" md="6" class="text-right">
                  <div>
                    <b>{{ item.total }}</b>
                  </div>
                  <div>
                    <small :class="item?.status ? 'success--text' : 'grey--text'">
                      {{ item?.status || "Pending" }}
                    </small>
                  </div>
                </v-col>
              </v-row>
            </template>
          </v-data-table>
        </v-container>
      </v-col>
      <v-col class="pt-3"
        ><v-toolbar class="primary" flat dense>
          <div>
            <v-btn class="primary darken-1" small
              ><v-icon small color="white">mdi-pencil</v-icon> Edit</v-btn
            >
  
            <v-menu bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn class="primary darken-1" small v-bind="attrs" v-on="on">
                  Print/PDF <v-icon>mdi-chevron-down</v-icon>
                </v-btn>
              </template>
  
              <v-list width="140" dense>
                <v-list-item
                  @click="openExternalWindowForInvoice(selectedItem, 'print')"
                >
                  <v-list-item-title style="cursor: pointer">
                    Print
                  </v-list-item-title>
                </v-list-item>
                <v-list-item
                  @click="openExternalWindowForInvoice(selectedItem, 'pdf')"
                >
                  <v-list-item-title style="cursor: pointer">
                    PDF
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
        </v-toolbar>
        <v-container
          style="
            background: white !important;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 50px;
          "
        >
          <v-card
            flat
            style="
              width: 100%;
              max-width: 820px; /* Adjust width as needed */
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            "
          >
            <iframe
              :src="pdfUrl + '#toolbar=0'"
              width="100%"
              height="800"
              style="border: none; background: white"
            ></iframe>
          </v-card>
        </v-container>
      </v-col>
    </v-row>
  </template>
  
  <script>
  let date = new Date();
  
  let d = date.getDate();
  let m = (date.getMonth() + 1).toString().padStart(2, "0");
  let y = date.getFullYear();
  let currentDate = y + "-" + m + "-" + d;
  
  export default {
    data: () => ({
      Model: "Invoice",
      endpoint: "invoice-v1",
      currentDate,
      filters: {},
      options: {},
      loading: false,
      response: "",
      expenses: [],
      errors: [],
      headers: [
        {
          text: "Customer",
          value: "customer",
        },
      ],
      componentKey: 1,
      selectedItem: null,
    }),
    async created() {
      this.getDataFromApi();
    },
    computed: {
      pdfUrl() {
        if (!this.selectedItem) return null;
        let { id, invoice_type } = this.selectedItem;
        return `https://backend.myhotel2cloud.com/api/invoice-${invoice_type}-print/${id}`;
      },
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
      openExternalWindowForInvoice(selectedItem, model = "print") {
        if (!selectedItem) return;
        let { id, invoice_type } = selectedItem;
        let url = `https://backend.myhotel2cloud.com/api/invoice-${invoice_type}-${model}/${id}`;
        let element = document.createElement("a");
        element.setAttribute("target", "_blank");
        element.setAttribute("href", url);
        document.body.appendChild(element);
        element.click();
      },
      async getDataFromApi() {
        this.loading = true;
        let { data } = await this.$axios.get(this.endpoint);
        this.loading = false;
        this.expenses = data.data;
        if (data.data.length) {
          this.selectedItem = data.data[0];
        }
      },
    },
  };
  </script>
  