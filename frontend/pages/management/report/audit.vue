<template>
  <v-container>
    <!-- Summary Section -->
    <v-row>
      <!-- Room Status -->
      <v-col v-for="item in roomData" :key="item.label" cols="12" sm="4" md="2">
        <v-card :color="item.color" class="pa-3" tile>
          <v-card-title class="white--text">
            {{ item.label }}
          </v-card-title>
          <v-card-subtitle class="white--text">
            {{ item.value }}
          </v-card-subtitle>
        </v-card>
      </v-col>

      <!-- Guest Meals -->
      <v-col v-for="item in guestData" :key="item.label" cols="12" sm="4" md="2">
        <v-card :color="item.color" class="pa-3" tile>
          <v-card-title class="white--text">
            {{ item.label }}
          </v-card-title>
          <v-card-subtitle class="white--text">
            {{ item.value }}
          </v-card-subtitle>
        </v-card>
      </v-col>
    </v-row>

    <!-- Income & Expense Section -->
    <v-row>
      <!-- Income -->
      <v-col v-for="item in incomeData" :key="item.label" cols="12" sm="4" md="2">
        <v-card :color="item.color" class="pa-3" tile>
          <v-card-title class="white--text">
            {{ item.label }}
          </v-card-title>
          <v-card-subtitle class="white--text">
            {{ item.value }}
          </v-card-subtitle>
        </v-card>
      </v-col>

      <!-- Expense -->
      <v-col v-for="item in expenseData" :key="item.label" cols="12" sm="4" md="2">
        <v-card :color="item.color" class="pa-3" tile>
          <v-card-title class="white--text">
            {{ item.label }}
          </v-card-title>
          <v-card-subtitle class="white--text">
            {{ item.value }}
          </v-card-subtitle>
        </v-card>
      </v-col>
    </v-row>

    <!-- Profit/Loss Section -->
    <v-row>
      <v-col v-for="item in profitLossData" :key="item.label" cols="12" sm="4" md="2">
        <v-card :color="item.color" class="pa-3" tile>
          <v-card-title class="white--text">
            {{ item.label }}
          </v-card-title>
          <v-card-subtitle class="white--text">
            {{ item.value }}
          </v-card-subtitle>
        </v-card>
      </v-col>
    </v-row>

    <!-- Bookings By Source -->
    <v-row>
      <v-col v-for="item in bookingsSourceData" :key="item.label" cols="12" sm="4" md="2">
        <v-card :color="item.color" class="pa-3" tile>
          <v-card-title class="white--text">
            <v-icon :color="item.color">{{ item.icon }}</v-icon> {{ item.label }}
          </v-card-title>
          <v-card-subtitle class="white--text">
            {{ item.value }} ({{ item.percentage }})
          </v-card-subtitle>
        </v-card>
      </v-col>
    </v-row>

    <!-- Balance Sheet -->
    <v-row>
      <v-col v-for="(item, index) in balanceSheetData.values" :key="index" cols="12" sm="6" md="3">
        <v-card :color="item.color || 'grey'" class="pa-3" tile>
          <v-card-title class="white--text">
            {{ item.value }}
          </v-card-title>
        </v-card>
      </v-col>
    </v-row>

  </v-container>
</template>

<script>
export default {
  data() {
    return {
      roomData: [
        { label: "Checkin", value: 1, color: "#ffc000" },
        { label: "Continue", value: 85, color: "#03c1ec" },
        { label: "Day Use", value: 0, color: "#71de36" },
        { label: "Complementary", value: 0, color: "#800080" },
        { label: "Checkout", value: 0, color: "#dc3545" },
        { label: "Closing", value: 0, color: "#a6a6a6" },
      ],
      guestData: [
        { label: "Breakfast", value: 0, color: "green" },
        { label: "Lunch", value: 0, color: "purple" },
        { label: "Dinner", value: 0, color: "orange" },
      ],
      incomeData: [
        { label: "Cash", value: 22344, color: "green" },
        { label: "Card", value: 0, color: "purple" },
        { label: "Online", value: 0, color: "orange" },
        { label: "Bank", value: 0, color: "red" },
        { label: "UPI", value: 0, color: "teal" },
        { label: "Cheque", value: 0, color: "blue" },
        { label: "Total", value: 22344, color: "grey" },
      ],
      expenseData: [
        { label: "Cash", value: 100, color: "green" },
        { label: "Card", value: 0, color: "purple" },
        { label: "Online", value: 0, color: "orange" },
        { label: "Bank", value: 0, color: "red" },
        { label: "UPI", value: 0, color: "teal" },
        { label: "Cheque", value: 0, color: "blue" },
        { label: "Total", value: 100, color: "grey" },
      ],
      profitLossData: [
        { label: "Loss", value: 0, color: "red" },
        { label: "Profit", value: 22144, color: "green" },
        { label: "City Ledger", value: 3360, color: "orange" },
      ],
      bookingsSourceData: [
        { icon: "mdi-walk", value: 0, percentage: "0%", label: "Walk", col: 7, color: "green" },
        { icon: "mdi-laptop", value: 0, percentage: "0%", label: "OTA", col: 7, color: "rgb(0, 217, 255)" },
        { icon: "mdi-account-tie", value: 0, percentage: "0%", label: "Corp", col: 7, color: "orange" },
        { icon: "mdi-cloud-outline", value: 0, percentage: "0%", label: "Web", col: 7, color: "purple" },
        { icon: "mdi-gift-outline", value: 0, percentage: "0%", label: "Comp", col: 7, color: "pink" },
        { icon: "mdi-account-outline", value: 0, percentage: "0%", label: "Agent", col: 7, color: "teal" },
        { icon: "---", value: "", percentage: "100.00%", label: "Vacant 36 (100.00%)", col: 7, color: "#c5c5c5" },
      ],
      balanceSheetData: {
        values: [
          { value: 22344 },
          { value: 0 },
          { value: 100 },
          { value: 100 },
          { value: "Cash in Hand (22244)", color: "green" },
        ],
        totals: [
          { value: 22344, colspan: 2 },
          { value: 200, colspan: 2 },
          { value: "Profit/Loss (22144)", color: "green" },
        ],
      },
    };
  },
};
</script>