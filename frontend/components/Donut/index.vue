<template>
  <div :style="`width:${size}`" v-if="series.length" class="d-flex">
    <apexchart
      type="donut"
      :options="chartOptions"
      :series="series"
    ></apexchart>

    <div style="width: 100%;display: flex; flex-direction: column; justify-content: center">
      <div
        v-for="(item, index) in labels"
        :key="index"
        style="line-height: 16px; display: flex; align-items: center"
      >
        <small style="font-size: 10px"
          ><span><v-badge dot left :color="item.color"></v-badge></span>
          {{ item.text }} ({{
            item.value == 0 ? 0 : parseInt(item.value)
          }})</small
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["compId", "labels", "colors", "size"],

  data: () => ({
    series: [],
    chartOptions: {
      legend: false,
      dataLabels: {
        enabled: false,
      },
    },
  }),

  mounted() {
    this.series = this.labels.map((e) =>
      e.value == 0 ? 10 : parseInt(e.value)
    );
  },
};
</script>
