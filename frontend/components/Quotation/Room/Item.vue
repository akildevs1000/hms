<template>
  <v-dialog persistent v-model="itemDialog" max-width="400">
    <AssetsIconClose left="390" @click="close" />
    <template v-slot:activator="{ on, attrs }">
      <v-hover v-slot:default="{ hover, props }">
        <span v-bind="props">
          <v-btn v-bind="attrs" v-on="on" x-small elevation="0"
            ><v-icon small color="primary">mdi-plus-circle</v-icon>
            <span class="text-color">New Row</span></v-btn
          >
        </span>
      </v-hover>
    </template>
    <v-card>
      <v-alert flat class="grey lighten-3" dense>
        <span class="text-color">Item Information</span></v-alert
      >
      <v-card-text>
        <v-row>
          <v-col cols="12">
            <v-text-field
              label="Description"
              dense
              outlined
              v-model="item.room_type"
              :hide-details="true"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Food"
              dense
              outlined
              v-model="item.meal_name"
              :hide-details="true"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Price"
              dense
              outlined
              v-model="item.price"
              :hide-details="true"
              required
              type="number"
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <v-text-field
              label="Pax"
              dense
              outlined
              v-model="item.no_of_adult"
              :hide-details="true"
              required
              type="number"
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <v-text-field
              label="No of Rooms"
              dense
              outlined
              v-model="item.no_of_rooms"
              :hide-details="true"
              required
              type="number"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="No of night"
              dense
              outlined
              v-model="item.no_of_nights"
              :hide-details="true"
              required
              type="number"
            ></v-text-field>
          </v-col>

          <v-col cols="12" class="text-center">
            <AssetsButton
              :options="{
                color: `red`,
                label: `Cancel`,
              }"
              @click="close"
            />
            &nbsp;
            <AssetsButton
              :options="{
                color: `blue`,
                label: `Confirm`,
              }"
              @click="confirm"
            />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["label"],
  data() {
    return {
      itemDialog: false,
      item: {
        room_type: "",
        meal_name: "",
        price: 0,
        no_of_adult: 0,
        no_of_rooms: 0,
        no_of_nights: 0,
        total_price: 0,
      },
      defaultItem: {
        room_type: "",
        meal_name: "",
        price: 0,
        no_of_adult: 0,
        no_of_rooms: 0,
        no_of_nights: 0,
        total_price: 0,
      },
    };
  },
  methods: {
    confirm() {
      this.$emit("selectedItem", this.item);
      this.close();
    },
    close() {
      this.item = this.defaultItem;
      this.itemDialog = false;
    },
  },
};
</script>
