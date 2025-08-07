<template>
  <v-container fluid>
    <v-data-table
      dense
      :headers="headers"
      :items="data"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [100, 500, 1000],
      }"
    >
      <template v-slot:item.date_time="{ item }">
        <v-container class="pa-1">
          <div class="d-flex align-center">
            <v-icon class="mr-2">mdi-clock-outline</v-icon>
            <div>
              <small class="d-block" style="line-height: 1.1">
                {{ $dateFormat.dmy(item.created_at) }} {{ item.start_time }}
              </small>
              <small class="d-block">
                {{ $dateFormat.dmy(item.created_at) }} {{ item.end_time }}
              </small>
            </div>
          </div>
        </v-container>
      </template>

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
      <template v-slot:item.voice_note="{ item }">
        <v-container class="pa-1">
          <audio
            v-if="item.voice_note"
            controls
            style="width: 250px; height: 40px"
          >
            <source :src="item.voice_note" />
          </audio>
          <span v-else>---</span>
          <!-- <v-icon v-else class="placeholder-icon">mdi-microphone-off</v-icon> -->
        </v-container>
      </template>
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
    data: [],
    errors: [],
    headers: [
      {
        text: "Room",
        value: "room.room_no",
      },

      {
        text: "Date/Time",
        value: "date_time",
        align: "center",
      },
      {
        text: "Before Attachment",
        value: "before_attachment",
      },
      {
        text: "After Attachment",
        value: "after_attachment",
      },
      {
        text: "Voice Note",
        value: "voice_note",
        align: "center",
      },
      {
        text: "Clean By",
        value: "cleaned_by_user.name",
      },
      {
        text: "Response by",
        value: "response_by_user.name",
      },
      {
        text: "Status",
        value: "status",
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

      this.data = data.data;
    },
  },
};
</script>
