<template>
  <div>
    <table>
      <tr>
        <th>Emirate Id</th>
        <td>
          {{ caps(emirateItems.emirate_id || "---") }}
        </td>
      </tr>

      <tr>
        <th>Name</th>
        <td>
          {{ emirateItems.name || "---" }}
        </td>
      </tr>

      <tr>
        <th>Gender</th>
        <td>
          {{ emirateItems.gender || "---" }}
        </td>
      </tr>

      <tr>
        <th>Date Of Birth</th>
        <td>
          {{ caps(emirateItems.date_of_birth || "---") }}
        </td>
      </tr>

      <tr>
        <th>Nationality</th>
        <td>
          {{ caps(emirateItems.nationality || "---") }}
        </td>
      </tr>
      <tr>
        <th>Issue</th>
        <td>
          {{ caps(emirateItems.issue || "---") }}
        </td>
      </tr>

      <tr>
        <th>Expiry</th>
        <td>
          {{ caps(emirateItems.expiry || "---") }}
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  props: ["emirateItems"],
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
