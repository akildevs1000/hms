<template>
  <div class="mt-8">
    <table>
      <tr>
        <th>Name</th>
        <td>
          {{ caps(BankInfo.bank_name) }}
        </td>
      </tr>

      <tr>
        <th>Account No</th>
        <td>
          {{ caps(BankInfo.account_no) }}
        </td>
      </tr>

      <tr>
        <th>Title</th>
        <td>
          {{ caps(BankInfo.account_title) }}
        </td>
      </tr>

      <tr>
        <th>Iban</th>
        <td>
          {{ caps(BankInfo.iban) }}
        </td>
      </tr>

      <tr>
        <th>Address</th>
        <td>
          {{ caps(BankInfo.address) }}
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  props: ["BankInfo"],
  data() {
    return {};
  },
  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    }
  }
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #fbfdff;
}
</style>
