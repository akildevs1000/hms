<template>
  <v-row v-if="labels.length">
    <v-col cols="5" style="padding-left: 0px">
      <div v-if="chartOptions.customTotalValue == 0" class="empty-doughnut1">
        0
      </div>
      <div v-else>
        <apexchart
          :key="key"
          style="margin: 0 auto; text-align: left; margin-left: -35px"
          :width="width ?? '170px'"
          :class="isCentered ? 'mx-auto' : ''"
          type="donut"
          legend="false"
          :options="chartOptions"
          :series="series"
        ></apexchart>
      </div>
    </v-col>
    <v-col
      v-if="!hideTable"
      cols="7"
      style="padding-left: 0px; margin: auto; font-size: 11px"
      class="pt-0"
    >
      <div v-for="(item, index) in labels" :key="index">
        <v-row>
          <v-col cols="8"
            ><v-icon :color="colors[index]">mdi mdi-square-medium</v-icon
            >{{ item.text }}</v-col
          ><v-col cols="4" style="padding-left: 0px">
            <div
              style="text-align: right"
              v-if="showPriceFormat && showPriceFormat == 'true'"
            >
              {{ getPriceFormat(item.value) }}
            </div>
            <div v-else>
              {{ item.value == 0 ? 0 : parseInt(item.value) }}
            </div>
          </v-col>
        </v-row>

        <v-divider color="#dddddd" />
      </div>
    </v-col>
    <!-- <div
      style="
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
      "
    >
      <div
        v-for="(item, index) in labels"
        :key="index"
        style="line-height: 16px; display: flex; align-items: center"
      >
        <v-row>
          <v-col cols="8"
            ><v-icon color="#92d050">mdi mdi-square-medium</v-icon
            >{{ item.text }}</v-col
          ><v-col cols="4" style="padding-left: 0px">
            {{ item.value == 0 ? 0 : parseInt(item.value) }}
          </v-col>
        </v-row>

        <v-divider color="#dddddd" />
      </div>

       
    </div> -->
  </v-row>
</template>

<script>
export default {
  props: [
    "compId",
    "labels",
    "colors",
    "size",
    "total",
    "width",
    "showPriceFormat",
    "hideTable",
    "isCentered",
  ],

  data: () => ({
    key: 1,
    series: [],
    chartOptions: {
      legend: false,
      colors: [],
      dataLabels: {
        enabled: false,
      },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              name: {
                show: false,
                fontSize: "22px",
                fontFamily: "Source Sans Pro , sans-serif !important",
                color: "#dfsda",
                offsetY: -10,
              },
              value: {
                show: true,
                fontSize: "16px",
                fontFamily: "Source Sans Pro , sans-serif !important",
                // fontWeight: "bold", // Make the total value bold
                color: "#8a8a8a",
                formatter: function (val) {
                  return val;
                },
              },
              total: {
                show: true,
                fontFamily: "Source Sans Pro , sans-serif !important",
                label: "Total",
                color: "#373d3f",
                formatter: function (val) {
                  return val.config.customTotalValue;
                  // console.log(showPriceFormat);
                  // if (showPriceFormat) {
                  //   return getPriceFormat(val.config.customTotalValue);
                  // } else return val.config.customTotalValue;
                },
              },
            },
          },
        },
      },
    },
  }),

  mounted() {
    // this.series = this.labels.map((e) =>
    //   e.value == 0 ? 10 : parseInt(e.value)
    // );
    // setTimeout(() => {
    //try {
    let counter = 0;
    this.chartOptions.labels = [];
    this.chartOptions.series = [];
    //this.series = [];
    //console.log(this.labels.length);
    let total = 0;
    this.labels.forEach((element) => {
      if (element.text == "Profit") console.log(element.value);
      this.chartOptions.labels[counter] = element.text;
      this.chartOptions.series[counter] = Math.abs(element.value); //
      this.chartOptions.colors[counter] = this.colors[counter];
      this.series[counter] = Math.abs(element.value); // (element.value + "").replace("-", "");
      total = total + parseInt(element.value);
      counter++;
    });
    // this.chartOptions.labels[0] = "Low";
    // this.chartOptions.series[0] = data["1"]?.length ?? 0;
    // this.chartOptions.labels[1] = "Medium";
    // this.chartOptions.series[1] = data["2"]?.length ?? 0;
    // this.chartOptions.labels[2] = "High";
    // this.chartOptions.series[2] = data["3"]?.length ?? 0;
    //this.chartOptions.customTotalValue = this.total;
    this.chartOptions.customTotalValue = total;

    // } catch (e) {}
    this.key += 1;

    // console.log(this.series);
    //}, 1000 * 1);
  },
  methods: {
    getPriceFormat(price) {
      let currency = this.$auth.user.company.currency ?? "";
      return (
        currency +
        "" +
        parseFloat(price).toLocaleString("en-IN", {
          maximumFractionDigits: 2,
        })
      );
    },
  },
};
</script>
