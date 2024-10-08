<template>
  <span>
    <AssetsTable height="300" :headers="headers" :items="filteredItems">
      <template #date="{ item }">
        {{ $dateFormat.dmy(item.date) || "---" }}
        <br />
        {{ item.day || "---" }}
      </template>
      <template #room="{ item }">
        {{ item.room_no || "---" }}
        <br />
        {{ item.room_type || "---" }}
      </template>
      <template #meal="{ item }">
        {{ item?.foodplan?.title || "---" }}
      </template>

      <template #extra_bed_qty="{ item }">
        {{ item.extra_bed_qty || "---" }}
      </template>

      <template #early_check_in="{ item }">
        {{ item.early_check_in ? "Yes" : "No" }}
      </template>

      <template #late_check_out="{ item }">
        {{ item.late_check_out ? "Yes" : "No" }}
      </template>

      <template #extras="{ item }">
        {{
          $utils.currency_format(
            parseFloat(item.extra_booking_hours_charges) +
              parseFloat(item.cleaning) +
              parseFloat(item.electricity) +
              parseFloat(item.generator) +
              parseFloat(item.audio) +
              parseFloat(item.projector)
          )
        }}
      </template>
      <template #total="{ item }">
        {{ $utils.currency_format(item.total) }}
      </template>
      <template #action="{ item }">
        <CustomerViewBookingHall
          v-if="booking.booking_type == 'hall'"
          :booking="booking"
          :item="item"
        />
        <CustomerViewBookingRoom v-else :booking="booking" :item="item" />
      </template>
    </AssetsTable>
  </span>
</template>
<script>
export default {
  props: ["orderRooms", "booking", "room_no"],
  data: () => ({
    headers: [],
    items: [],
  }),
  created() {
    if (this.booking.booking_type == "hall") {
      this.headers = [
        {
          text: `Date`,
          value: `date`,
          align: `center`,
        },
        { text: `Room`, value: `room`, align: `center` },
        { text: `Tariff`, value: `tariff`, align: `center` },
        { text: `Adults`, value: `no_of_adult`, align: `center` },
        { text: `Child`, value: `no_of_child`, align: `center` },
        { text: `Meal`, value: `meal`, align: `center` },
        { text: `Extras`, value: `extras`, align: `center` },
        { text: `Total`, value: `total`, align: `right` },
        { text: ``, value: `action`, align: `center`, width: "30px" },
      ];
    } else {
      this.headers = [
        {
          text: `Date`,
          value: `date`,
          align: `center`,
        },
        { text: `Room`, value: `room`, align: `center` },
        { text: `Tariff`, value: `tariff`, align: `center` },
        { text: `Adults`, value: `no_of_adult`, align: `center` },
        { text: `Child`, value: `no_of_child`, align: `center` },
        { text: `Meal`, value: `meal`, align: `center` },
        {
          text: `Extra Bed`,
          value: `extra_bed_qty`,
          align: `center`,
        },
        {
          text: `Early Checkin`,
          value: `early_check_in`,
          align: `center`,
        },
        {
          text: `Late Checkout`,
          value: `late_check_out`,
          align: `center`,
        },
        { text: `Total`, value: `total`, align: `right` },
        { text: ``, value: `action`, align: `center`, width: "30px" },
      ];
    }
  },
  computed: {
    filteredItems() {
      if (this.room_no > 0) {
        return this.orderRooms.filter((e) => e.room_no == this.room_no);
      }
      return this.orderRooms;
    },
  },
};
</script>
