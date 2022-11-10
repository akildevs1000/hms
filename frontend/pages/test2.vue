 <template>
  <div>
    <p>{{ status }}</p>
    <v-simple-table>
      <template v-slot:default>
        <thead>
          <tr>
            <th class="text-left font-weight-black" style="font-size: 15px">
              Employee
            </th>
            <th
              class="text-left font-weight-black"
              style="font-size: 20px"
              v-for="day in getDaysInCurrentMonth"
              :key="day"
            >
              <label
                :class="day == getDayInCurrentMonth ? 'primary--text' : ''"
                >{{ day }}</label
              >
            </th>
          </tr>
        </thead>
        <tbody>
          <!-- <p>{{ status[Math.floor(Math.random() * 2)] }}</p> -->

          <tr v-for="(item, index) in names" :key="index">
            <td>{{ item }}</td>
            <td
              :style="
                day == getDayInCurrentMonth ? 'background-color:#E0E0E0' : ''
              "
              v-for="day in getDaysInCurrentMonth"
              :key="day"
            >
              <v-icon
                v-if="status[randNo] == 'present'"
                :color="'success'"
                small
              >
                mdi-check-bold
              </v-icon>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
  </div>
</template>
<script>
export default {
  data() {
    return {
      data: [],
      currentDate: "",
      count: 0,
      vvv: [],
      names: [
        "Proteus",
        "Babette",
        "Radojka",
        "Juro",
        "Veerle",
        "Bertram",
        "Ilari",
        "Straton",
        "Diamanto",
        "Lisa",
      ],

      status: ["present", "absent"],
    };
  },

  created() {
    this.names = this.names.map(
      (e, i, arr) => arr[Math.floor(Math.random() * arr.length)]
    );

    // this.status = this.status.map(
    //   (e, i, arr) => arr[Math.floor(Math.random() * arr.length)]
    // );
  },

  computed: {
    getDaysInCurrentMonth() {
      const date = new Date();
      return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
    },

    getDayInCurrentMonth() {
      const date = new Date();
      return date.getDate();
    },

    randNo() {
      return Math.floor(Math.random() * 2);
    },
  },

  methods: {},
};
</script>

<style scoped>
table,
th,
td,
tbody {
  border-width: thin;

  border: 1px solid #e0e0e0;
}

th,
td {
  padding: 1px;
}
table,
th {
  text-align: center;
}

/* table .v-table tbody td {
  height: 40px;
  border: none !important;
  border-bottom: none !important;
}
.theme--light.v-table tbody tr:not(:last-child) {
  border-bottom: none !important;
}

.v-data-table .theme--light {
  border: none !important;
  border-bottom: none !important;
  border-bottom: none !important;
}

.v-data-table__wrapper {
  border: none !important;
  border-bottom: none !important;
  border-bottom: none !important;
} */
</style>
