<template>
  <div class="mt-12">
    <table>
      <tr>
        <th>Certificate</th>
        <td>
          {{ caps(qualification_list.certificate) }}
        </td>
      </tr>

      <tr>
        <th>Collage</th>
        <td>
          {{ caps(qualification_list.collage) }}
        </td>
      </tr>

      <tr>
        <th>Start</th>
        <td>
          {{ caps(qualification_list.start) }}
        </td>
      </tr>

      <tr>
        <th>End</th>
        <td>
          {{ caps(qualification_list.end) }}
        </td>
      </tr>

      <tr>
        <th>Type</th>
        <td>
          {{ caps(qualification_list.type) }}
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  props: ["qualification_list"],
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
