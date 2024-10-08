<template>
  <v-card dense>
    <v-card-text>
      <v-row>
        <v-col class="mt-0" style="font-size: 12px"
          >Next 10 Days Forecast
        </v-col>
        <v-col cols="12" class="mt-0 pa-0" style="min-height: 120px">
          <div
            style="
              display: flex;
              justify-content: center;
              gap: 18px;
              height: 90px;
            "
          >
            <div v-for="(item, index) in forecastData" :key="index">
              <div
                :title="item.availableCount"
                style="
                  width: 10px;
                  height: 90px;
                  background: #139c4a;
                  position: relative;
                "
              >
                <div
                  :title="item.bookedCount"
                  :style="`position: absolute;
                  bottom: 0;
                  width: 100%;
                  height: ${item.bookedPercent}%;
                  background: #71de36;
                  transition: height 0.5s ease;`"
                ></div>

                <div
                  style="position: absolute; bottom: -22px; font-size: 11px"
                  :style="getLabelStyle(item.label)"
                >
                  {{ item.label }}
                </div>
              </div>
            </div>
          </div>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  data: () => ({
    forecastData: [],
  }),
  async created() {
    let { data } = await this.$axios.get(
      `ten-days-forcast/${this.$auth.user.company_id}`
    );
    this.forecastData = data;
  },
  methods: {
    getLabelStyle(label) {
      const leftPosition = label === "Fri" || label === "Thu" ? "20%" : "60%";
      return `
        left: -${leftPosition};
      `;
    },
  },
};
</script>
