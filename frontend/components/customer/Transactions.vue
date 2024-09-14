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
        <tr class="table-header-text">
          <td class="blue--text">#</td>
          <td class="blue--text">Staff</td>
          <td class="blue--text">Date</td>
          <td class="blue--text">payment Mode</td>
          <td class="blue--text">Reference</td>
          <td class="blue--text">Description</td>
          <td class="blue--text">Debit</td>
          <td class="blue--text">Credit</td>
          <td class="blue--text">Balance</td>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(item, index) in transactions"
          :key="index"
          style="font-size: 13px"
          class="no-bg"
        >
          <td>
            <b>{{ index + 1 }}</b>
          </td>
          <td>
            {{ item.user?.name || "---" }}
            {{ item.user?.last_name }}
          </td>
          <td>
            {{ item.date || "---" }} <br />
            {{ item.time || "---" }}
          </td>

          <td>
            {{ (item && item.payment_mode && item.payment_mode.name) || "---" }}
          </td>
          <td>{{ item.reference_number || "---" }}</td>
          <td>{{ item.desc || "---" }}</td>
          <td class="text-right">
            {{
              item && item.debit == 0
                ? "---"
                : $utils.currency_format(item.debit)
            }}
          </td>
          <td class="text-right">
            {{
              item && item.credit == 0
                ? "---"
                : $utils.currency_format(item.credit)
            }}
          </td>
          <td class="text-right">
            {{
              item.balance == 0 ? "---" : $utils.currency_format(item.balance)
            }}
          </td>
        </tr>
        <tr style="font-size: 13px">
          <td colspan="8" class="text-right blue--text">Balance</td>
          <td class="text-right blue--text">
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
