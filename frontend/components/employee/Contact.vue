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
                  Local Country
                </v-toolbar>
              </th>
            </tr>

            <tr>
              <th>Address</th>
              <td>
                {{ caps(contactItem.local_address) }}
              </td>
            </tr>
            <tr>
              <th>Whatsapp Number</th>
              <td>
                {{ contactItem.whatsapp_number }}
              </td>
            </tr>
            <tr>
              <th>Phone Number</th>
              <td>
                {{ contactItem.phone_number }}
              </td>
            </tr>
            <tr>
              <th>Phone Relative Number</th>
              <td>
                {{ contactItem.phone_relative_number }}
              </td>
            </tr>

            <tr>
              <th>Relation</th>
              <td>
                {{ contactItem.relation }}
              </td>
            </tr>

            <tr>
              <th>City</th>
              <td>
                {{ caps(contactItem.local_city) }}
              </td>
            </tr>
            <tr>
              <th>Country</th>
              <td>
                {{ caps(contactItem.local_country) }}
              </td>
            </tr>
            <th colspan="2">
              <v-toolbar color="#E9E9E9" small dense flat rounded="2">
                Home Country
              </v-toolbar>
            </th>
            <tr>
              <th>Address</th>
              <td>
                {{ caps(contactItem.local_address) }}
              </td>
            </tr>
            <tr>
              <th>Whatsapp Number</th>
              <td>
                {{ caps(contactItem.whatsapp_number) }}
              </td>
            </tr>
            <tr>
              <th>Phone Number</th>
              <td>
                {{ caps(contactItem.phone_number) }}
              </td>
            </tr>
            <tr>
              <th>Phone Relative Number</th>
              <td>
                {{ caps(contactItem.phone_relative_number) }}
              </td>
            </tr>

            <tr>
              <th>Relation</th>
              <td>
                {{ caps(contactItem.relation) }}
              </td>
            </tr>

            <tr>
              <th>City</th>
              <td>
                {{ caps(contactItem.local_city) }}
              </td>
            </tr>
            <tr>
              <th>Country</th>
              <td>
                {{ caps(contactItem.local_country) }}
              </td>
            </tr>
          </table>
        </div>
      </v-responsive>
    </v-responsive>

    <!-- <table>
      <tr>
        <th colspan="2">
          <v-toolbar color="primary" small dense flat dark rounded="2">
            Local Country
          </v-toolbar>
        </th>
      </tr>

      <tr>
        <th>Address</th>
        <td>
          {{ caps(contactItem.local_address ) }}
        </td>
      </tr>
      <tr>
        <th>Whatsapp Number</th>
        <td>
          {{ contactItem.whatsapp_number  }}
        </td>
      </tr>
      <tr>
        <th>Phone Number</th>
        <td>
          {{ contactItem.phone_number  }}
        </td>
      </tr>
      <tr>
        <th>Phone Relative Number</th>
        <td>
          {{ contactItem.phone_relative_number  }}
        </td>
      </tr>

      <tr>
        <th>Relation</th>
        <td>
          {{ contactItem.relation  }}
        </td>
      </tr>

      <tr>
        <th>City</th>
        <td>
          {{ caps(contactItem.local_city ) }}
        </td>
      </tr>
      <tr>
        <th>Country</th>
        <td>
          {{ caps(contactItem.local_country ) }}
        </td>
      </tr>
      <th colspan="2">
        <v-toolbar color="primary" small dense flat dark rounded="2">
          Local Country
        </v-toolbar>
      </th>
      <tr>
        <th>Address</th>
        <td>
          {{ caps(contactItem.local_address ) }}
        </td>
      </tr>
      <tr>
        <th>Whatsapp Number</th>
        <td>
          {{ contactItem.whatsapp_number  }}
        </td>
      </tr>
      <tr>
        <th>Phone Number</th>
        <td>
          {{ contactItem.phone_number  }}
        </td>
      </tr>
      <tr>
        <th>Phone Relative Number</th>
        <td>
          {{ contactItem.phone_relative_number  }}
        </td>
      </tr>

      <tr>
        <th>Relation</th>
        <td>
          {{ contactItem.relation  }}
        </td>
      </tr>

      <tr>
        <th>City</th>
        <td>
          {{ caps(contactItem.local_city ) }}
        </td>
      </tr>
      <tr>
        <th>Country</th>
        <td>
          {{ caps(contactItem.local_country ) }}
        </td>
      </tr>
    </table> -->
  </div>
</template>

<script>
export default {
  props: ["contactItem"],
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
