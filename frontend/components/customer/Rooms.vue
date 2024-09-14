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
      <thead>
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
          <!-- <td class="blue--text text-right">Price</td> -->
          <td class="blue--text text-right">Total</td>
          <td class="blue--text text-right"></td>
        </tr>
      </thead>

      <tbody>
        <tr
          style="font-size: 13px"
          v-for="(item, index) in orderRooms"
          :key="index"
        >
          <td>{{ ++index || "---" }}</td>
          <td>
            {{ $dateFormat.dmy(item.date) || "---" }} <br />
            {{ item.day || "---" }}
          </td>
          <td>
            {{ item.room_no || "---" }} <br />{{ item.room_type || "---" }}
          </td>
          <td>{{ item.tariff }}</td>
          <td>{{ item.no_of_adult }}</td>
          <td>{{ item.no_of_child }}</td>
          <td>
            {{
              item?.foodplan?.title +
                " (" +
                item?.foodplan?.unit_price +
                ") " || "---"
            }}
          </td>
          <td class="text-right">
            {{ item.bed_amount || "---" }}
          </td>
          <td class="text-right">
            {{ item.early_check_in || "---" }}
          </td>
          <td class="text-right">
            {{ item.late_check_out || "---" }}
          </td>
          <!-- <td class="text-right">
            {{ $utils.currency_format(item.price) || "---" }}
          </td> -->
          <td class="text-right">
            {{ $utils.currency_format(item.total) || "---" }}
          </td>
          <td class="blue--text text-right">
            <CustomerViewBooking :booking="booking" :item="item" />
          </td>
        </tr>
      </tbody>
    </table>
  </span>
</template>
<script>
export default {
  props: ["orderRooms", "booking"],
};
</script>
