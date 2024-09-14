<template>
  <div class="text-center">
    <v-dialog v-model="extraAmountPopUp" width="350">
      <v-card>
        <v-alert class="primary" dense dark>
          <v-row>
            <v-col> Set Extra Amount </v-col>
            <v-col>
              <div class="text-right">
                <v-icon @click="extraAmountPopUp = false">mdi-close</v-icon>
              </div>
            </v-col>
          </v-row>
        </v-alert>
        <v-card-text>
          <v-radio-group v-model="discountType">
            <v-row no-gutters>
              <v-col cols="12">
                <div style="display: flex">
                  <v-radio dense label="Percentage" value="percent"></v-radio>
                  <div style="width: 70px" class="ml-5 pa-1">
                    <input
                      style="
                        height: 100px;
                        border: 1px solid #dddddd;
                        width: 100%;
                        border-radius: 5px;
                      "
                      v-model="discountValue"
                      v-if="discountType == 'percent'"
                    />
                  </div>
                  <div class="mt-2" v-if="discountType == 'percent'">%</div>
                </div>
              </v-col>
              <v-col cols="12">
                <div style="display: flex">
                  <v-radio
                    dense
                    label="Direct Price Reduction"
                    value="direct"
                  ></v-radio>
                  <div style="width: 70px" class="ml-5 pa-1">
                    <input
                      style="
                        height: 100px;
                        border: 1px solid #dddddd;
                        width: 100%;
                        border-radius: 5px;
                      "
                      v-model="discountValue"
                      v-if="discountType == 'direct'"
                    />
                  </div>
                </div>
              </v-col>
              <v-col>
                <v-btn
                  :disabled="sub_total == 0"
                  class="primary"
                  x-small
                  @click="calc"
                  >Confirm</v-btn
                >
              </v-col>
            </v-row>
          </v-radio-group>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: ["sub_total"],
  data() {
    return {
      discountType: "percent",
      extraAmountPopUp: false,
      discountValue: 10,

      result: 0,
    };
  },
  methods: {
    calc() {
      let dt = this.discountType;
      let dv = this.discountValue;

      if (dt == "direct") {
        this.$emit("extraAddedAmount", Math.abs(dv));
        this.result = Math.abs(dv);
        this.extraAmountPopUp = false;
        return;
      }

      this.$emit(
        "extraAddedAmount",
        Math.abs((this.sub_total * dv) / 100)
      );
      this.result =  Math.abs((this.sub_total * dv) / 100);

      this.extraAmountPopUp = false;

    },
  },
};
</script>
