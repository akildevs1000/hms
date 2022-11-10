<template>
  <v-btn small class="primary darken-2" @click="export_csv">
    <v-icon class="mr-1" small>mdi-file</v-icon>
    CSV
  </v-btn>
</template>
<script>
export default {
  props: ["data", "headers"],
  methods: {
    json_to_csv(json) {
      let header = this.headers.map(e => e.text).join(",") + "\n";

      let data = json;

      let rows = "";

      data.forEach(e => {
        rows +=
          Object.values(e)
            .join(",")
            .trim() + "\n";
      });

      return header + rows;
    },

    export_csv() {
      if (this.data.length == 0) {
        this.snackbar = true;
        this.response = "No record to download";
        return;
      }

      let csvData = this.json_to_csv(this.data);

      let e = document.createElement("a");
      e.setAttribute(
        "href",
        "data:text/csv;charset=utf-8, " + encodeURIComponent(csvData)
      );
      e.setAttribute("download", "download.csv");
      document.body.appendChild(e);
      e.click();
      document.body.removeChild(e);
    }
  }
};
</script>
