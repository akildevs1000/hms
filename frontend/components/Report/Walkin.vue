<template>
  <span>
    <v-row no-gutters>
      <v-col cols="7">
        <table>
          <thead>
            <tr>
              <td class="text-center">
                <small>COLOR</small>
              </td>
              <td><small>SOURCE</small></td>
              <td class="text-center"><small>NO OF ROOM</small></td>
              <td class="text-center"><small>PERCENTAGE %</small></td>
              <td class="text-center"><small>REVENUE</small></td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in data" :key="index">
              <td class="text-center">
                <v-icon :style="{ color: item.color, fontSize: '24px' }"
                  >mdi-circle</v-icon
                >
              </td>
              <td>{{ item.source }}</td>
              <td class="text-center">{{ item.no_of_room }}</td>
              <td class="text-center">{{ item.percentage }}</td>
              <td class="text-center">{{ item.revenue }}</td>
            </tr>
          </tbody>
        </table>
      </v-col>
      <v-col cols="5" class="text-center">
        <v-tabs v-if="!loading" right>
          <v-tab>pie chart </v-tab>
          <v-tab>statistical </v-tab>
          <v-tab-item>
            <highcharts :options="pieChartOptions"></highcharts>
          </v-tab-item>
          <v-tab-item>
            <highcharts :options="barChartOptions"></highcharts>
          </v-tab-item>
        </v-tabs>
      </v-col>
    </v-row>
    <div ref="capture" style="position: fixed; top: -9999px; left: -9999px">
      <v-container>
        <v-row>
          <v-col cols="12">
            <table>
              <thead>
                <tr>
                  <td class="text-center">
                    <small>COLOR</small>
                  </td>
                  <td><small>SOURCE</small></td>
                  <td class="text-center"><small>NO OF ROOM</small></td>
                  <td class="text-center"><small>PERCENTAGE %</small></td>
                  <td class="text-center"><small>REVENUE</small></td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in data" :key="index">
                  <td class="text-center">
                    <v-icon :style="{ color: item.color, fontSize: '24px' }"
                      >mdi-circle</v-icon
                    >
                  </td>
                  <td>{{ item.source }}</td>
                  <td class="text-center">{{ item.no_of_room }}</td>
                  <td class="text-center">{{ item.percentage }}</td>
                  <td class="text-center">{{ item.revenue }}</td>
                </tr>
                <tr>
                  <td colspan="2">TOTAL</td>
                  <td class="text-center">{{ totalRooms }}</td>
                  <td class="text-center">100%</td>
                  <td class="text-center">{{ totalRevenue }}</td>
                </tr>
              </tbody>
            </table>
          </v-col>
          <v-col cols="6" class="text-center">
            <v-card outlined>
              <highcharts :options="pieChartOptions"></highcharts>
            </v-card>
          </v-col>
          <v-col cols="6" class="text-center">
            <v-card outlined>
              <highcharts :options="barChartOptions"></highcharts>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </span>
</template>

<script>
import html2canvas from "html2canvas";
import jsPDF from "jspdf";

export default {
  props: {
    heading: {
      default: "Revenue Flow Chat ( Source )",
    },
    data: {
      default: () => [],
    },
    colors: {
      default: () => [],
    },
  },

  data() {
    return {
      clicked: false,
      loading: true,
      pieChartOptions: {
        chart: {
          type: "pie",
          //   height: "320",
          options3d: {
            enabled: true,
            alpha: 45,
            beta: 0,
          },
        },
        colors: [],

        title: {
          text: "",
        },
        plotOptions: {
          pie: {
            depth: 45,
          },
        },
        series: [
          {
            name: "Data",
            data: [],
          },
        ],
      },

      barChartOptions: {
        chart: {
          type: "column",
        },
        title: {
          text: "",
        },
        xAxis: {
          categories: [],
          crosshair: true,
        },
        yAxis: {
          min: 0,
          title: {
            text: "",
          },
        },
        tooltip: {
          headerFormat:
            '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat:
            '<tr><td style="padding:0"><b>{point.y:.2f}</b></td></tr>',
          footerFormat: "</table>",
          shared: true,
          useHTML: true,
        },
        plotOptions: {
          column: {
            pointPadding: 0.2,
            borderWidth: 0,
          },
        },
        legend: {
          enabled: false,
        },
        series: [
          {
            data: [],
          },
        ],
      },
    };
  },
  mounted() {
    if (!this.data.length) {
      return;
    }

    const dataSet = this.data.map(({ source, revenue, color }) => ({
      source,
      revenue: parseFloat(revenue),
      color,
    }));

    // Update series and colors for the chart
    this.pieChartOptions.series[0].data = dataSet.map((data) => [
      data.source,
      data.revenue,
    ]);

    this.pieChartOptions.colors = this.colors;
    // Update xAxis categories and series for the bar chart
    this.barChartOptions.xAxis.categories = dataSet.map((data) => data.source);
    this.barChartOptions.series[0].data = dataSet.map((data) => ({
      y: data.revenue,
      color: data.color,
    }));

    this.loading = false;
  },

  computed: {
    totalRooms() {
      return this.data.reduce(
        (sum, item) => sum + parseFloat(item.no_of_room),
        0
      );
    },
    totalRevenue() {
      return this.data
        .reduce((sum, item) => sum + parseFloat(item.revenue), 0)
        .toFixed(2);
    },
  },
  methods: {
    async captureArea() {
      this.clicked = true;
      try {
        const scale = 2; // Increase the scale to capture a higher resolution image
        const canvas = await html2canvas(this.$refs.capture, {
          scale,
          useCORS: true, // Enable this if your content includes external images
        });
        const imgData = canvas.toDataURL("image/png");

        const pdf = new jsPDF({
          orientation: "portrait",
          unit: "pt", // Use points as the unit to match CSS pixels
          format: [canvas.width, canvas.height],
        });

        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
        pdf.save("report.pdf");
      } catch (err) {
        console.error("Error capturing area:", err);
      }
    },
  },
};
</script>

<style scoped>
@import url("@/assets/css/tableStyles.css");
</style>
