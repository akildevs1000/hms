<template>
  <span v-if="!loading">
    <div class="text-right">
      <div class="mb-1">
        <AssetsIcon
          icon="printer-outline"
          @click="process_file('expect_checkout_report_print')"
        />
      </div>
      </div>
      <AssetsTable :headers="headers" :items="items" />
  </span>
</template>
<script>
export default {
  props: ["data"],
  data() {
    return {
      loading: true,
      errors: [],
      headers: [],
      items: [],
    };
  },

  created() {
    this.headers = [
      { text: `Room No`, value: `room_no`, align: `center` },
      { text: `Type`, value: `room_type`, align: `center` },
      { text: `Guest`, value: `title`, align: `center` },
      { text: `C/In`, value: `check_in`, align: `center` },
      { text: `C/Out`, value: `check_out`, align: `center` },
      { text: `Group`, value: `group`, align: `center` },
      { text: `Source`, value: `source`, align: `center` },
      { text: `Paid By`, value: `paid_by`, align: `center` },
      { text: `Posting`, value: `postings`, align: `right` },
      { text: `Total`, value: `total`, align: `right` },
      { text: `Paid`, value: `paid`, align: `right` },
      { text: `Balance`, value: `balance`, align: `right` },
    ];

    this.items = this.data.map((e) => ({
      room_no: e.room_no,
      room_type: e.room_type.name,
      title: e.booked_room.title,
      check_in: e.booked_room.check_in,
      check_out: e.booked_room.check_out,
      group: e?.booked_room?.booking?.group_name == "yes" ? "Yes" : "-",
      source: e?.booked_room?.booking?.type,
      paid_by: this.getPaidBy(e?.booked_room?.booking?.type),
      postings: this.$utils.currency_format(
        this.$utils.getSum(e.booked_room.postings.map((e) => e.amount_with_tax))
      ),
      total: this.$utils.currency_format(e?.booked_room?.grand_total),
      paid: this.$utils.currency_format(e.booked_room.booking.paid_amounts),
      balance: this.$utils.currency_format(e.booked_room.booking.balance),
    }));

    this.loading = false;
  },

  methods: {
    getPaidBy(paid_by) {
      let json = {
        1: "Hotel",
        2: "Agent",
      };
      return json[paid_by] || "---";
    },
    process(type) {
      let comId = this.$auth.user.company.id;
      let date = new Date().toJSON().slice(0, 10);
      let url =
        process.env.BACKEND_URL + `${type}?company_id=${comId}&date=${date}`;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
    },
  },
};
</script>
