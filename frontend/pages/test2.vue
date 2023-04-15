<template>
  <v-card class="dashboard-payment-card" color="primary" dark>
    <v-card-title class="text-uppercase font-weight-bold white--text">
      Payment Received
    </v-card-title>
    <v-card-text>
      <v-row align="center">
        <v-col cols="3">
          <v-icon large>mdi-currency-usd</v-icon>
        </v-col>
        <v-col cols="9">
          <div class="text-h1 font-weight-bold white--text">
            {{ 100000.23 }}
          </div>
          <div class="text-subtitle-2 white--text">
            {{ "Total Payment Received" }}
          </div>
        </v-col>
      </v-row>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
      <v-btn text color="white" @click="$emit('view-details')"
        >View Details</v-btn
      >
      <v-spacer></v-spacer>
      <v-btn icon color="white"><v-icon>mdi-dots-vertical</v-icon></v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  name: "DashboardPaymentCard",
  props: {
    amount: {
      type: Number,
      required: true,
    },
    subtitle: {
      type: String,
      default: "Total Payment Received",
    },
  },
};
</script>

<style scoped>
.dashboard-payment-card {
  max-width: 350px;
  border-radius: 10px;
  box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.25);
}

.dashboard-payment-card .v-card__title {
  padding: 10px;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.dashboard-payment-card .v-card__text {
  padding: 10px;
}

.dashboard-payment-card .v-divider {
  margin: 10px 0;
}

.dashboard-payment-card .v-card__actions button {
  font-weight: bold;
}

.dashboard-payment-card .v-card__actions button:hover {
  background-color: rgba(255, 255, 255, 0.2);
  color: #fff;
}

.dashboard-payment-card .v-btn--icon:hover {
  background-color: rgba(255, 255, 255, 0.2);
}
</style>
