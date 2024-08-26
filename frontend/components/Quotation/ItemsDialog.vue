<template>
  <v-dialog v-model="dialog" persistent max-width="400">
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        small
        color="primary"
        class="white--text"
        dark
        v-bind="attrs"
        v-on="on"
      >
        <v-icon color="white" small> mdi-plus </v-icon> {{ label }}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar flat class="primary white--text" dense>
        Quotaion <v-spacer></v-spacer
        ><v-icon @click="dialog = false" color="white"
          >mdi-close</v-icon
        ></v-toolbar
      >
      <v-tabs right>
        <v-tab>Room </v-tab>
        <v-tab>Hall </v-tab>
        <v-tab-item>
          <QuotationRoom
            label="Add Room"
            @emitRoomData="handleTableDataForRoom"
          />
        </v-tab-item>
        <v-tab-item>
          <QuotationHall
            label="Add Hall"
            @emitHallData="handleTableDataForHall"
          />
        </v-tab-item>
      </v-tabs>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["label"],
  data: () => ({
    dialog: false,
  }),
  methods: {
    handleTableDataForRoom(e) {
      this.$emit(`tableData`, e);
      this.dialog = false;
    },

    handleTableDataForHall(e) {
      this.$emit(`tableData`, e);
      this.dialog = false;
    },
  },
};
</script>
