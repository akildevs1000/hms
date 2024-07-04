// import Vue from "vue";
// import VueApexCharts from "vue-apexcharts";

// Vue.use(VueApexCharts);

import Vue from "vue";
import VueApexCharts from "vue-apexcharts";
Vue.component('apexchart', VueApexCharts);

import HighchartsVue from 'highcharts-vue';
import Highcharts from 'highcharts';
import Highcharts3D from 'highcharts/highcharts-3d';
import HighchartsMore from 'highcharts/highcharts-more';

HighchartsMore(Highcharts);
Highcharts3D(Highcharts);

Vue.use(HighchartsVue);



// import HighchartsVue from 'highcharts-vue'
// Vue.use(HighchartsVue)