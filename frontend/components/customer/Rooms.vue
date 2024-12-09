<template>
  <span>
    <AssetsTable height="300" :headers="headers" :items="filteredItems">
      <template #date="{ item }">
        {{ item.date || "---" }}
        <br />
        {{ item.day || "---" }}
      </template>
      <template #tariff="{ item }">
        {{ item.tariff || "---" }}
      </template>
      <!-- <template #total_with_tax="{ item }">
        {{ $utils.currency_format(parseFloat(item.price || 0) + parseFloat(item.room_tax || 0)) }}
      </template> -->

      <!-- <template #discount="{ item }">
        <span class="red--text"
          >-{{
            $utils.currency_format(parseFloat(item.room_discount || 0))
          }}</span
        >
      </template> -->
      <!-- <template #room_tax="{ item }">
        {{ $utils.currency_format(parseFloat(item.room_tax || 0)) }}
      </template> -->
      <!-- <template #after_discount="{ item }">
        {{ $utils.currency_format(parseFloat(item.after_discount || 0)) }}
      </template> -->
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
            parseFloat(item.extra_booking_hours_charges || 0) +
              parseFloat(item.cleaning || 0) +
              parseFloat(item.electricity || 0) +
              parseFloat(item.generator || 0) +
              parseFloat(item.audio || 0) +
              parseFloat(item.projector || 0)
          )
        }}
      </template>
      <template #action="{ item }">
        <CustomerViewBookingHall
          v-if="booking.booking_type == 'hall'"
          :booking="booking"
          :item="item"
        />
        <CustomerViewBookingRoom v-else :booking="booking" :item="item" :totalRooms="orderRooms && orderRooms.length" />
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
        { text: `Discount`, value: `discount`, align: `center` },
        { text: `Adults`, value: `no_of_adult`, align: `center` },
        { text: `Child`, value: `no_of_child`, align: `center` },
        { text: `Meal`, value: `meal`, align: `center` },
        { text: `Extras`, value: `extras`, align: `center` },
        { text: `Total (excl. discount,add)`, value: `total`, align: `right` },
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
          text: `E.C/In`,
          value: `early_check_in`,
          align: `center`,
        },
        {
          text: `L.C/Out`,
          value: `late_check_out`,
          align: `center`,
        },
        { text: `Room Price`, value: `total_with_tax`, align: `center` },
        { text: `Total`, value: `total`, align: `center` },
        // { text: `Tax`, value: `room_tax`, align: `center` },
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
