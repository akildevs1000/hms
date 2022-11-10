<template>
  <div>
    <table>
      <tr>
        <th>Passport No</th>
        <td>
          {{ caps(passport_list.passport_no || "---") }}
        </td>
      </tr>

      <tr>
        <th>Date of Birth</th>
        <td>
          {{ passport_list.place_of_issues || "---" }}
        </td>
      </tr>

      <tr>
        <th>Country</th>
        <td>
          {{ passport_list.country || "---" }}
        </td>
      </tr>

      <tr>
        <th>Issues Date</th>
        <td>
          {{ caps(passport_list.issue_date || "---") }}
        </td>
      </tr>

      <tr>
        <th>Expiry Date</th>
        <td>
          {{ caps(passport_list.expiry_date || "---") }}
        </td>
      </tr>
      <tr>
        <th>Note</th>
        <td>
          {{ caps(passport_list.note || "---") }}
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  props: ["passport_list"],
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
