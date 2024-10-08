<template>
  <v-card dense>
    <v-card-text>
      <v-row no-gutter>
        <v-col> <v-icon left color="blue">mdi-chat</v-icon> Chat </v-col>
        <v-col cols="12">
          <v-simple-table dense>
            <template v-slot:default>
              <tbody>
                <tr v-for="(item, index) in items" :key="index">
                  <td style="font-size: 11px">{{ item.room_no }}</td>
                  <td style="font-size: 11px">{{ item.time }}</td>
                  <td style="font-size: 11px">{{ item.service }}</td>
                  <td style="font-size: 11px">{{ item.time }}</td>
                  <td style="font-size: 11px">
                    <v-icon small :color="item.iconColor">{{ item.icon }}</v-icon>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      items: [
        {
          room_no: 201,
          time: "17:54",
          order: "OR 4512",
          service: "Food",
          actions: "open",
          icon: "mdi-lock-open-outline",
          iconColor: "red",
        },
        {
          room_no: 112,
          time: "17:45",
          order: "OR 4510",
          service: "Misc",
          actions: "close",
          icon: "mdi-lock-outline",
          iconColor: "green",
        },
        {
          room_no: 405,
          time: "17:30",
          order: "OR 4508",
          service: "Service",
          actions: "forward",
          icon: "mdi-lock-outline",
          iconColor: "purple",
        },
      ],
    };
  },
};
</script>
