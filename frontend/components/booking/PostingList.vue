<template>
  <AssetsTable v-if="postings.length" :headers="headers" :items="postings">
    <template #posting_date="{ item }">
      {{ $dateFormat.dmy(item.posting_date) }}
    </template>
    <template #amount_with_tax="{ item }">
      {{ $utils.currency_format(item.amount_with_tax) }}
    </template>
    <template #row>
      <tr>
        <td colspan="4" class="text-right border-bottom primary--text py-1 px-2">
          Total Balance &nbsp;&nbsp;&nbsp;
          {{ $utils.currency_format(totalAmount) }}
        </td>
      </tr>
    </template>
  </AssetsTable>
</template>
<script>
export default {
  props: ["room_id"],
  data() {
    return {
      viewPostingDialog: false,
      postings: [],
      headers: [],
    };
  },
  computed: {
    totalAmount() {
      return this.postings.reduce((acc, cur) => acc + cur.amount_with_tax, 0);
    },
  },
  async created() {
    await this.get_posting();
  },

  methods: {
    async get_posting() {
      let { data } = await this.$axios.get(`posting/${this.room_id}`, {
        params: {
          company_id: this.$auth.user.company.id,
        },
      });

      this.headers = [
        { text: `Date`, value: `posting_date`, align: `left` },
        { text: `Item`, value: `item`, align: `left` },
        { text: `Qty`, value: `qty`, align: `left` },
        { text: `Amount`, value: `amount_with_tax`, align: `right` },
      ];
      this.postings = data;
    },
  },
};
</script>
