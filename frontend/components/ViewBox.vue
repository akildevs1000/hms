<template>
  <v-dialog v-model="viewBoxDialog" max-width="900px">
    <v-card>
      <v-card-title class="text-h5">
        <v-spacer></v-spacer
        ><v-icon @click="viewBoxDialog = false" color="primary"
          >mdi-close</v-icon
        ></v-card-title
      >
      <v-container>
        <v-row>
          <!-- Profile Picture -->
          <v-col cols="7">
            <v-card class="pa-2" outlined style="border-radius: 30px">
              <img
                style="height: 470px; width: 100%"
                :src="
                  customer?.captured_photo ||
                  'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU'
                "
                alt="Front Image"
              />
            </v-card>
          </v-col>

          <!-- ID Cards -->
          <v-col cols="5">
            <img
              style="
                width: 100%;
                border: 1px solid #e0e0e0;
                border-radius: 10px;
                padding: 1px;
              "
              :src="
                customer?.id_frontend_side ||
                'https://th.bing.com/th/id/OIP.4aA4OdutpLizseoj0B9M9wHaE9?w=265&h=180&c=7&r=0&o=5&pid=1.7'
              "
            />
            <img
              style="
                width: 100%;
                border: 1px solid #e0e0e0;
                border-radius: 10px;
                padding: 1px;
              "
              :src="
                customer?.id_backend_side ||
                'https://th.bing.com/th/id/OIP.4aA4OdutpLizseoj0B9M9wHaE9?w=265&h=180&c=7&r=0&o=5&pid=1.7'
              "
            />
          </v-col>
          <v-col class="text-center">
            <v-btn
              small
              elevation="0"
              color="grey lighten-3"
              class="ma-2"
              @click="downloadCustomerAttachments"
            >
              <v-icon left color="primary"> mdi-printer </v-icon>
              Print ID
            </v-btn>

            <v-btn
              small
              elevation="0"
              color="grey lighten-3"
              class="ma-2"
              @click="process('grc_report_print')"
            >
              <v-icon left color="primary"> mdi-file </v-icon>
              Print GRC
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: ["customer", "id"],
  data: () => ({
    viewBoxDialog: false,
    GRCDialog: false,
  }),
  methods: {
    downloadCustomerAttachments() {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute(
        "href",
        `${process.env.BACKEND_URL}download_customer_attachments/${this.id}`
      );
      document.body.appendChild(element);
      element.click();
    },
    process(url) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute(
        "href",
        `${process.env.BACKEND_URL}${url}/${this.id}`
      );
      document.body.appendChild(element);
      element.click();
    },
  },
};
</script>
