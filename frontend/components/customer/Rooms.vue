<template>
  <span>
    <style scoped>
      .simple-table {
        width: 100%;
        border-collapse: collapse;
      }
      .simple-table td {
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        padding: 5px;
        text-align: center;
      }
    </style>
    <table class="simple-table mt-0">
      <TableHeader :cols="headers" />
      <!-- <thead>
        <tr>
          <td class="blue--text">No</td>
          <td class="blue--text">Date</td>
          <td class="blue--text">Room</td>
          <td class="blue--text">Tariff</td>
          <td class="blue--text">Adults</td>
          <td class="blue--text">Child</td>
          <td class="blue--text">Meal</td>
          <td class="blue--text">Extra Bed</td>
          <td class="blue--text">Early Checkin</td>
          <td class="blue--text">Late Checkout</td>
          <td class="blue--text text-right">Total</td>
          <td class="blue--text text-right"></td>
        </tr>
      </thead> -->

      <tbody>
        <tr
          style="font-size: 13px"
          v-for="(item, index) in orderRooms"
          :key="index"
        >
          <td>
            <small>{{ ++index || "---" }}</small>
          </td>
          <td>
            <small>{{ $dateFormat.dmy(item.date) || "---" }}</small> <br />
            <small>{{ item.day || "---" }}</small>
          </td>
          <td>
            <small>{{ item.room_no || "---" }}</small> <br /><small>{{
              item.room_type || "---"
            }}</small>
          </td>
          <td>
            <small>{{ item.tariff }}</small>
          </td>
          <td>
            <small>{{ item.no_of_adult }}</small>
          </td>
          <td>
            <small>{{ item.no_of_child }}</small>
          </td>
          <td>
            <small>{{ item?.foodplan?.title || "---" }}</small>
          </td>
          <template v-if="booking.booking_type == 'hall'">
            <td>
              <small>{{
                $utils.currency_format(
                  parseFloat(item.extra_booking_hours_charges) +
                    parseFloat(item.cleaning) +
                    parseFloat(item.electricity) +
                    parseFloat(item.generator) +
                    parseFloat(item.audio) +
                    parseFloat(item.projector)
                )
              }}</small>
            </td>
          </template>
          <template v-else>
            <td>
              <small>{{ item.extra_bed_qty || "---" }}</small>
            </td>
            <td>
              <small>{{ item.early_check_in ? "Yes" : "No" }}</small>
            </td>
            <td>
              <small>{{ item.late_check_out ? "Yes" : "No" }}</small>
            </td>
          </template>

          <td class="text-right">
            <small>{{ $utils.currency_format(item.total) || "---" }}</small>
          </td>
          <td class="blue--text text-right">
            <small>
              <CustomerViewBookingHall
                v-if="booking.booking_type == 'hall'"
                :booking="booking"
                :item="item" />

              <CustomerViewBookingRoom v-else :booking="booking" :item="item"
            /></small>
          </td>
        </tr>
      </tbody>
    </table>
  </span>
</template>
<script>
export default {
  props: ["orderRooms", "booking"],
  data: () => ({
    headers: [],
  }),
  created() {
    if (this.booking.booking_type == "hall") {
      this.headers = [
        `No`,
        `Date`,
        `Room`,
        `Tariff`,
        `Adults`,
        `Child`,
        `Meal`,
        `Extras `,
        `Total`,
        ``,
      ];
    } else {
      this.headers = [
        `No`,
        `Date`,
        `Room`,
        `Tariff`,
        `Adults`,
        `Child`,
        `Meal`,
        `Extra Bed`,
        `Early Checkin`,
        `Late Checkout`,
        `Total`,
        ``,
      ];
    }
  },
};
</script>
