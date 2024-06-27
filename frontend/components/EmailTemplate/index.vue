<template>
  <span>
    <!-- <v-row>
      <v-col cols="3" v-for="(item, index) in items" :key="index">
        <v-card>
          <v-card-title>
            <span class="headline">{{ item.name }}</span>
            <v-spacer></v-spacer
            ><v-icon right color="primary" small>mdi-pencil</v-icon>
          </v-card-title>
          <v-card-text>
            <div>
              <span v-html="item.body" label="Body" rows="5" required></span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row> -->
    <v-data-table
      dense
      :headers="headers"
      :items="items"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [3, 5, 15],
      }"
      class="elevation-1 mt-1 pa-3"
    >
      <template v-slot:top>
        <v-toolbar flat dense class="mb-5">
          {{ Model }}
          <v-icon color="primary" right class="mt-1" @click="getDataFromApi()"
            >mdi-reload</v-icon
          >
          <v-spacer></v-spacer>
          <EmailTemplateCreate
            :model="Model"
            :endpoint="endpoint"
            @response="getDataFromApi"
          />
        </v-toolbar>
      </template>
      <template v-slot:item.body="{ item }">
        <span v-html="item.body"></span>
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
                <EmailTemplateView
                  :model="Model"
                  :endpoint="endpoint"
                  :item="item"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <EmailTemplateEdit
                  :model="Model"
                  :endpoint="endpoint"
                  :item="item"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <EmailTemplateDelete
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
    Model: "Template",
    endpoint: "template",
    currentDate,
    filters: {},
    options: {},
    loading: false,
    response: "",
    items: [],
    errors: [],
    headers: [
      {
        text: "Name",
        value: "name",
      },
      {
        text: "Action",
        value: "action",
      },
      {
        text: "Created At",
        value: "created_at",
      },
      {
        text: "Updated At",
        value: "updated_at",
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
    // async getVendorCategory() {
    //   let { data } = await this.$axios.get(this.endpoint);

    //   return data;
    // },
    async getDataFromApi() {
      this.loading = true;
      let { data } = await this.$axios.get(
        `${this.endpoint}?company_id=` + this.$auth.user.company_id
      );
      this.loading = false;

      this.items = data.data;
    },
  },
};
</script>
