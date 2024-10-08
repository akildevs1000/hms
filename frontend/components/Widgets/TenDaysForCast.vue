<template>
  <v-card dense>
    <v-card-text >
      <v-row >
        <v-col class="mt-0">Next 10 Days Forecast </v-col>
        <v-col cols="12" class="mt-0 pa-0" style="min-height: 120px;">
          <div
            style="
              display: flex;
              justify-content: center;
              gap: 20px;
              height: 90px;
            "
          >
            <div
              style="
                width: 10px;
                height: 90px;
                background: #139c4a;
                position: relative;
              "
              v-for="(item,index) in forecastData"
              :key="index"
            >
              <div
                style="
                  position: absolute;
                  bottom: 0;
                  width: 100%;
                  height: 60%;
                  background: #71de36;
                  transition: height 0.5s ease;
                "
              ></div>

              <div style="position: absolute; bottom: -22px; left: -60%;font-size: 11px;">
                {{item.label}}
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
    forecastData: [
      {
        label: "Sun",
        value: 70,
      },
      {
        label: "Mon",
        value: 75,
      },
      {
        label: "Tue",
        value: 80,
      },
      {
        label: "Wed",
        value: 85,
      },
      {
        label: "Thu",
        value: 90,
      },
      {
        label: "Fri",
        value: 85,
      },
      {
        label: "Sat",
        value: 80,
      },
      {
        label: "Sun",
        value: 75,
      },
      {
        label: "Mon",
        value: 70,
      },
      {
        label: "Tue",
        value: 65,
      },
    ],
  }),
};
</script>
