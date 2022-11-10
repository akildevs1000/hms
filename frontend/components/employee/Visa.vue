<template>
  <div>
    <v-responsive class="overflow-y-auto" max-height="700">
      <v-responsive height="100vh" class="text-center pa-2">
        <v-responsive min-height="0vh"></v-responsive>
        <div class="text-center text-body-2 mb-12">
          <table>
            <tr>
              <th colspan="2">
                <v-toolbar color="#E9E9E9" small dense flat rounded="2">
                  Visa
                </v-toolbar>
              </th>
            </tr>

            <tr>
              <th>Visa No</th>
              <td>
                {{ caps(visaItem.visa_no) }}
              </td>
            </tr>
            <tr>
              <th>Place Of Issues</th>
              <td>
                {{ caps(visaItem.place_of_issues) }}
              </td>
            </tr>
            <tr>
              <th>Country</th>
              <td>
                {{ caps(visaItem.country) }}
              </td>
            </tr>
            <tr>
              <th>Issue Date</th>
              <td>
                {{ caps(visaItem.issue_date) }}
              </td>
            </tr>

            <tr>
              <th>Expiry Date</th>
              <td>
                {{ caps(visaItem.expiry_date) }}
              </td>
            </tr>

            <tr>
              <th colspan="2">
                <v-toolbar color="#E9E9E9" small dense flat rounded="2">
                  Labour
                </v-toolbar>
              </th>
            </tr>
            <tr>
              <th>Labour No</th>
              <td>
                {{ caps(visaItem.labour_no) }}
              </td>
            </tr>
            <tr>
              <th>Personal No</th>
              <td>
                {{ caps(visaItem.personal_no) }}
              </td>
            </tr>
            <tr>
              <th>Issue Date</th>
              <td>
                {{ caps(visaItem.labour_issue_date) }}
              </td>
            </tr>
            <tr>
              <th>Expiry Date</th>
              <td>
                {{ caps(visaItem.labour_expiry_date) }}
              </td>
            </tr>

            <tr>
              <th>Note</th>
              <td>
                {{ caps(visaItem.note) }}
              </td>
            </tr>
          </table>
        </div>
      </v-responsive>
    </v-responsive>
  </div>
</template>

<script>
export default {
  props: ["visaItem"],
  data() {
    return {
      benched: 0
    };
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
