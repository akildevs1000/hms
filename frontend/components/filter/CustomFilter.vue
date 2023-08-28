<template>
  <div>
    <v-row>
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify" v-model="search"
          hide-details></v-text-field>
      </v-col>
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-select v-model="filterType" :items="[
          {
            id: 1,
            name: 'Today',
          },
          {
            id: 2,
            name: 'Yesterday',
          },
          {
            id: 3,
            name: 'This Week',
          },
          {
            id: 4,
            name: 'This Month',
          },
          {
            id: 5,
            name: 'Custom',
          },
        ]" dense placeholder="Type" outlined :hide-details="true" item-text="name" item-value="id"></v-select>
      </v-col>

      <v-col md="3" v-if="filterType == 5">
        <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
          offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
              class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
          </template>
          <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3" v-if="filterType == 5">
        <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y
          min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense class="custom-text-box shadow-none"
              solo flat label="To" :hide-details="true"></v-text-field>
          </template>
          <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      // -------------------end chart ----------------

      from_date: "",
      from_menu: false,

      to_date: "",
      to_menu: false,
      loading: false,

      filterType: 1,
      search: "",
    };
  },

  watch: {
    filterType() {
      this.FilterData();
    },
    search() {
      this.FilterData();
    },
    from_date() {
      this.FilterData();
    },
    to_date() {
      this.FilterData();
    },
  },

  mounted() { },

  methods: {
    commonMethod() {
      if (this.from_date && this.to_date) {
      }
    },

    FilterData() {
      let data = {
        from: this.from_date,
        to: this.to_date,
        type: this.filterType,
        search: this.search,
      };
      this.$emit("filter-attr", data);
    },
  },
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.custom-text-box {
  border-radius: 2px !important;
  border: 1px solid #dbdddf !important;
}

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select:focus {
  outline: none !important;
  border-color: #5fafa3;
  box-shadow: 0 0 0px #5fafa3;
}
</style>
