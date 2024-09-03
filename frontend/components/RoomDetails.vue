<template>
  <v-dialog v-model="RoomDetailDialog" max-width="460">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <v-icon color="primary" small> mdi-eye </v-icon><small class="ml-2">View</small>
      </span>
    </template>
    <v-card>
      <v-toolbar flat class="primary white--text" dense>
        Rooms {{room_type}} <v-spacer></v-spacer
        ><v-icon @click="RoomDetailDialog = false" color="white"
          >mdi-close</v-icon
        ></v-toolbar
      >
      <v-container>
        <v-tabs show-arrows>
          <v-tab v-for="(item, index) in filteredSelectedRooms" :key="index">{{
            item.room_no
          }}</v-tab>
          <v-tab-item v-for="(item, index) in filteredSelectedRooms" :key="index">
           <v-container>
            <div
              class="px-5 pt-2 d-flex justify-space-between"
              style="font-size: 16px; color: #aaaaaa"
            >
              <span> Room - {{ item.room_no }}</span>
              <span> {{ item.room_type }}</span>
            </div>
            <v-divider></v-divider>
            <section class="payment-section">
              <div class="input-group input-group-sm px-5 pt-2">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Amount
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ convert_decimal(item.price) }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Meal
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ convert_decimal(item.food_plan_price) }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Early Checkin
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ convert_decimal(item.early_check_in) }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Late Checkout
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ convert_decimal(item.late_check_out) }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Discount
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ convert_decimal(item.room_discount) }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Extra Amount
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ convert_decimal(item.room_extra_amount) }}
                </div>
              </div>
              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  After Dis.
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ convert_decimal(item.after_discount) }}
                </div>
              </div>
              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Grand Total
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ convert_decimal(item.total_price) }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Discount Reason
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ item.discount_reason || "---" }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Amount Reason
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ item.extra_amount_reason || "---" }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Adult
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ item.no_of_adult }}
                </div>
              </div>

              <div class="input-group input-group-sm px-5">
                <span class="input-group-text" id="inputGroup-sizing-sm">
                  Child
                </span>
                <div
                  type="text"
                  class="form-control"
                  aria-label="Sizing example input"
                  aria-describedby="inputGroup-sizing-sm"
                  disabled
                >
                  {{ item.no_of_child }}
                </div>
              </div>
            </section>
           </v-container>
          </v-tab-item>
        </v-tabs>
      </v-container>
    </v-card>
  </v-dialog>
</template>
<script>
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["label", "selectedRooms","room_type"],
  data: () => ({
    RoomDetailDialog: false,
    filteredSelectedRooms:[],
  }),
  created(){
    this.filteredSelectedRooms = this.selectedRooms.filter(e => e.room_type == this.room_type);
  },
  methods: {
    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },
  },
  computed: {
    formattedCheckinDate() {
      if (!this.temp.check_in) return "";

      const date = new Date(this.temp.check_in);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 12:00`;
    },
    formattedCheckOutDate() {
      if (!this.temp.check_out) return "";

      const date = new Date(this.temp.check_out);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 11:00`;
    },
  },
};
</script>
