<template>
  <v-dialog v-model="viewPostingDialog" persistent max-width="900px">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> View Posting </span>
    </template>
    <v-card>
      <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
        <span>View Posting</span>
        <v-spacer></v-spacer>
        <AssetsButtonClose @close="viewPostingDialog = false" />
      </v-toolbar>
      <v-card-text>
        <v-container>
          <AssetsTable :headers="headers" :items="postings" />
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["evenIid"],
  data() {
    return {
      viewPostingDialog: false,
      postings: [],
      headers: [],
    };
  },

  async created() {
    await this.get_posting();
  },

  methods: {
    async get_posting() {
      let { data } = await this.$axios.get(`posting/${this.evenIid}`, {
        params: {
          company_id: this.$auth.user.company.id,
        },
      });
      this.headers = [
        { text: `#`, value: `serial`, align: `left` },
        { text: `Bill`, value: `bill_no`, align: `left` },
        { text: `Room`, value: `room_no`, align: `left` },
        { text: `Room Type`, value: `room_type`, align: `left` },
        { text: `Title`, value: `title`, align: `left` },
        { text: `Item`, value: `item`, align: `left` },
        { text: `Qty`, value: `qty`, align: `left` },
        { text: `Amount`, value: `amount`, align: `left` },
        { text: `Posting Date`, value: `posting_date`, align: `left` },
      ];
      this.postings = data.map((e, i) => ({
        serial: i + 1,
        bill_no: e.bill_no,
        room_no: e.booked_room.room_no,
        room_type: e.booked_room.room_type,
        title: e.booked_room.title,
        item: e.item,
        qty: e.qty,
        amount: this.$utils.currency_format(e.amount_with_tax),
        posting_date: this.$dateFormat.dmy(e.posting_date),
      }));
    },
  },
};
</script>
