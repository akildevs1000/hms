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
      <TableHeader :cols="[
        `No`,
        `Bill`,
        `Date`,
        `Room Type`,
        `Room`,
        `Item`,
        `QTY`,
        `Amount`,
        `Sgst`,
        `Cgst`,
        `Total`,
        ``,
      ]" />
      <!-- <thead>
        <tr class="table-header-text">
          <td class="blue--text">No</td>
          <td class="blue--text">Bill</td>
          <td class="blue--text">Date</td>
          <td class="blue--text">Room Type</td>
          <td class="blue--text">Room</td>
          <td class="blue--text">Item</td>
          <td class="blue--text">QTY</td>
          <td class="blue--text text-right">Amount</td>
          <td class="blue--text text-right">Sgst</td>
          <td class="blue--text text-right">Cgst</td>
          <td class="blue--text text-right">Total</td>
          <td class="blue--text text-right"></td>
        </tr> 
      </thead>-->
      <tbody>
        <tr
          style="font-size: 13px"
          v-for="(item, postingIndex) in postings"
          :key="postingIndex"
        >
          <td><small>{{ ++postingIndex }}</small></td>
          <td><small>{{ item.bill_no || "---" }}</small></td>
          <td><small>{{ item.posting_date || "---" }}</small></td>
          <td>
           <small> {{
              (item.room && item.room.room_type && item.room.room_type.name) ||
              "---"
            }}</small>
          </td>
          <td><small>{{ (item.room && item.room.room_no) || "---" }}</small></td>
          <td><small>{{ item.item || "---" }}</small></td>
          <td><small>{{ item.qty || "---" }}</small></td>
          <td class="text-right">
            <small>{{ $utils.currency_format(item.single_amt) || "---" }}</small>
          </td>
          <td class="text-right">
           <small> {{ $utils.currency_format(item.sgst) || "---" }}</small>
          </td>
          <td class="text-right">
            <small>{{ $utils.currency_format(item.cgst) || "---" }}</small>
          </td>
          <td class="text-right">
            <small>{{ $utils.currency_format(item.amount_with_tax) || "---" }}</small>
          </td>
          <td class="text-center">
            <CustomerDetailPopup
              :bill_no="item.bill_no"
              :room_no="item?.room?.room_no"
              :full_name="full_name"
              :items="postings"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </span>
</template>
<script>
export default {
  props: ["postings", "full_name"],
};
</script>
