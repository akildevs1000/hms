<template>
  <div>
    <table>
      <!-- <tr v-for="(item, index) in personalItem" ::key="index">
        <th>{{ caps(index) || "---" }}</th>
        <td>
          {{ caps(item) || "---" }}
        </td>
      </tr> -->

      <tr>
        <th>Nationality</th>
        <td>
          {{ caps(personalItem.nationality || "---") }}
        </td>
      </tr>

      <tr>
        <th>Religion</th>
        <td>
          {{ caps(personalItem.religion || "---") }}
        </td>
      </tr>
      <tr>
        <th>Marital Status</th>
        <td>
          {{
            personalItem.marital_status
              ? personalItem.marital_status == 1
                ? "Married"
                : "Single"
              : "---"
          }}
        </td>
      </tr>
      <tr>
        <th>Date of Birth</th>
        <td>
          {{ personalItem.date_of_birth || "---" }}
        </td>
      </tr>

      <tr>
        <th>No. of Children</th>
        <td>
          {{ personalItem.no_of_children || "---" }}
        </td>
      </tr>

      <tr>
        <th>Father Name</th>
        <td>
          {{ caps(personalItem.father_name || "---") }}
        </td>
      </tr>
      <tr>
        <th>Gender</th>
        <td>
          <span v-if="personalItem.gender == 1">
            Male
          </span>
          <span v-else-if="personalItem.gender == 2">
            Female
          </span>
          <span v-else>
            ---
          </span>
        </td>
      </tr>
      <tr>
        <th>Mother Name</th>
        <td>
          {{ caps(personalItem.mother_name || "---") }}
        </td>
      </tr>
      <tr>
        <th>Employment of Spouse</th>
        <td>
          {{ caps(personalItem.no_of_spouse || "---") }}
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  props: ["personalItem"],
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
