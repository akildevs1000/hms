<template>
  <span v-if="!loading">
    <div class="text-right">
      <v-icon small color="primary" @click="process('inhouse_report_print')"
        >mdi-printer-outline</v-icon
      >
      <!-- <v-icon
          color="black"
          right
          @click="process('checkin_report_download')"
          >mdi-printer-outline</v-icon
        > -->
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
      { text: `Adult`, value: `no_of_adult`, align: `center` },
      { text: `Children`, value: `no_of_child`, align: `center` },
      { text: `Notes`, value: `notes`, align: `center` },
    ];

    this.items = this.data.map((e) => ({
      room_no: e.room_no,
      room_type: e.room_type.name,
      title: e.booked_room.title,
      check_in: e.booked_room.check_in,
      check_out: e.booked_room.check_out,
      group: e?.booked_room?.booking?.group_name == "yes" ? "Yes" : "-",
      source: e?.booked_room?.booking?.type,
      no_of_adult: e.booked_room.no_of_adult,
      no_of_child: e.booked_room.no_of_child,
      notes: e.booked_room.booking.request,
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
