<template>
  <span>
    <v-card flat>
      <v-card-text class="pa-5">Next 10 Days Forecast</v-card-text>
      <v-card-text>
        <div
          class="fill-height"
          style="display: flex; justify-content: center; align-items: center"
        >
          <div>
            <!-- Container for heading + bar chart -->
            <div style="display: flex; align-items: center; gap: 20px">
              <!-- Heading on the left -->

              <!-- Bar chart -->
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
                    height: ${
                      item.bookedPercent > 100 ? 100 : item.bookedPercent
                    }%;
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
            </div>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </span>
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
