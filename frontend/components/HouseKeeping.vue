<template>
  <v-container fluid>
    <v-data-table
      dense
      :headers="headers"
      :items="company_documents"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [100, 500, 1000],
      }"
    >
      <!-- <template v-slot:top>
      <v-toolbar flat dense class="mb-5">
        {{ Model }}
        <v-icon color="primary" right @click="getDataFromApi()"
          >mdi-reload</v-icon
        >
        <v-spacer></v-spacer>
        <CompanyDocumentCreate
          :model="Model"
          :endpoint="endpoint"
          @response="getDataFromApi"
        />
      </v-toolbar>
    </template> -->

      <template v-slot:item.before_attachment="{ item }">
        <ImageView
          v-if="item.before_attachment"
          :src="item.before_attachment"
        />
        <span v-else>---</span>
      </template>

      <template v-slot:item.after_attachment="{ item }">
        <ImageView v-if="item.after_attachment" :src="item.after_attachment" />
        <span v-else>---</span>
      </template>

      <!-- <template v-slot:item.options="{ item }">
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
              <CompanyDocumentView
                :model="Model"
                :endpoint="endpoint"
                :item="item"
                @response="getDataFromApi"
              />
            </v-list-item-title>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>
              <CompanyDocumentEdit
                :model="Model"
                :endpoint="endpoint"
                :item="item"
                @response="getDataFromApi"
              />
            </v-list-item-title>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>
              <CompanyDocumentDelete
                :id="item.id"
                :endpoint="endpoint"
                @response="getDataFromApi"
              />
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </template> -->
    </v-data-table>
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
    Model: "House Keeping",
    endpoint: "room-data",
    currentDate,
    filters: {},
    options: {},
    loading: false,
    response: "",
    company_documents: [],
    errors: [],
    headers: [
      {
        text: "Room",
        value: "room.room_no",
      },
      {
        text: "Status",
        value: "status",
      },
      {
        text: "Start Time",
        value: "start_time",
      },
      {
        text: "End Time",
        value: "end_time",
      },
      {
        text: "Total Time",
        value: "total_time",
      },
      {
        text: "Before Attachment",
        value: "before_attachment",
      },
      {
        text: "after Attachment",
        value: "after_attachment",
      },
      {
        text: "Clean By",
        value: "cleaned_by_user.name",
      },
      {
        text: "Response by",
        value: "response_by_user.name",
      },
      //   {
      //     text: "Action",
      //     align: "center",
      //     sortable: false,
      //     value: "options",
      //   },
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
    getRandomId() {
      return ++this.componentKey;
    },
    async getDataFromApi() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.loading = true;
      let { data } = await this.$axios.get(this.endpoint, config);
      this.loading = false;

      this.company_documents = data.data;
    },
  },
};
</script>
