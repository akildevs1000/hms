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
      <TableHeader
        :cols="[
          `#`,
          `Staff`,
          `Date`,
          `payment Mode`,
          `Reference`,
          `Description`,
          `Debit`,
          `Credit`,
          `Balance`,
        ]"
      />
      <!-- <thead>
        <tr class="table-header-text">
          <td class="primary--text"><small>#</small></td>
          <td class="primary--text"><small>Staff</small></td>
          <td class="primary--text"><small>Date</small></td>
          <td class="primary--text"><small>payment Mode</small></td>
          <td class="primary--text"><small>Reference</small></td>
          <td class="primary--text"><small>Description</small></td>
          <td class="primary--text"><small>Debit</small></td>
          <td class="primary--text"><small>Credit</small></td>
          <td class="primary--text"><small>Balance</small></td>
        </tr>
      </thead> -->
      <tbody>
        <tr
          v-for="(item, index) in transactions"
          :key="index"
          style="font-size: 13px"
          class="no-bg"
        >
          <td>
            <small>{{ index + 1 }}</small>
          </td>
          <td>
            <small>{{ item.user?.name || "---" }}</small>
            <br />
            <small>{{ item.user?.last_name }}</small>
          </td>
          <td>
            <small>{{ item.date || "---" }}</small> <br />
            <small> {{ item.time || "---" }}</small>
          </td>

          <td>
            <small>{{
              (item && item.payment_mode && item.payment_mode.name) || "---"
            }}</small>
          </td>
          <td>
            <small>{{ item.reference_number || "---" }}</small>
          </td>
          <td>
            <small>{{ item.desc || "---" }}</small>
          </td>
          <td class="text-right">
            <small>{{
              item && item.debit == 0
                ? "---"
                : $utils.currency_format(item.debit)
            }}</small>
          </td>
          <td class="text-right">
            <small>
              {{
                item && item.credit == 0
                  ? "---"
                  : $utils.currency_format(item.credit)
              }}</small
            >
          </td>
          <td class="text-right">
            <small>
              {{
                item.balance == 0 ? "---" : $utils.currency_format(item.balance)
              }}</small
            >
          </td>
        </tr>
        <tr style="font-size: 13px">
          <td colspan="8" class="text-right blue--text">Balance</td>
          <td class="text-right red--text">
            {{ $utils.currency_format(totalTransactionAmount) }}
          </td>
        </tr>
      </tbody>
    </table>
  </span>
</template>
<script>
export default {
  props: ["transactions", "totalTransactionAmount"],
};
</script>
