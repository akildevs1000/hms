<template>
  <table cellspacing="0" style="width: 100%">
    <thead style="background-color: #f2f2f2; width: 100%">
      <tr style="background-color: #f2f2f2; width: 100%">
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>Date</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>Room</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>Tariff</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>Adult</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>Child</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>Meal</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>E. Bed</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>E. C/in</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>L. C/out</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="text-center py-2"
        >
          <small>Total</small>
        </td>
        <td
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
        ></td>
      </tr>
    </thead>
    <tbody v-if="priceListTableView.length > 0">
      <tr v-for="(item, index) in priceListTableView" :key="index">
        <td class="text-center py-2">
          {{ formatDate(item.date) }} <br />
          {{ item.day }}
        </td>
        <td class="text-center py-2">
          {{ item.room_no }} <br />
          {{ item.room_type }}
        </td>
        <td class="text-center py-2">
          {{ item.day_type }}
        </td>
        <td class="text-center py-2">{{ item.no_of_adult }}</td>
        <td class="text-center py-2">{{ item.no_of_child }}</td>
        <td class="text-center py-2">{{ item.meal_name }}</td>
        <td class="text-center py-2">
          {{ item.extra_bed_qty || "-" }}
        </td>
        <td class="text-center py-2">
          {{ item.early_check_in > 0 ? "Yes" : "-" }}
        </td>
        <td class="text-center py-2">
          {{ item.late_check_out > 0 ? "Yes" : "-" }}
        </td>
        <td class="text-right py-2">
          {{ convert_decimal(item.total_price) }}
        </td>
        <td class="text-center">
          <v-menu
            nudge-bottom="50"
            nudge-left="20"
            transition="scale-transition"
            origin="center center"
            bottom
            left
            min-width="90"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn icon v-bind="attrs" v-on="on">
                <v-icon small>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>

            <v-card>
              <v-list>
                <v-list-item>
                  <RoomDetails
                    :key="roomDetailsCompKey"
                    :room_type="item.room_type"
                    :selectedRooms="selectedRooms"
                  />
                </v-list-item>
                <!-- <v-list-item>
                  <RoomEditDialog
                    label="Edit"
                    :options="room"
                    @tableData="handleTableData"
                  />
                </v-list-item> -->
                <v-list-item @click="deleteItem(index, item)">
                  <v-icon small color="red">mdi-close</v-icon
                  ><small class="ml-2">Delete</small>
                </v-list-item>
              </v-list>
            </v-card>
          </v-menu>
        </td>
      </tr>
    </tbody>
  </table>
</template>
<script>
export default {
  props: ["priceListTableView"],
  methods: {
    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },
    formatDate(date) {
      let dateObj = new Date(date);

      let day = dateObj.getDate().toString().padStart(2, "0");
      let month = dateObj
        .toLocaleString("en-GB", { month: "short" })
        .slice(0, 3);
      let year = dateObj.getFullYear();

      return `${day} ${month} ${year}`;
    },
  },
};
</script>
