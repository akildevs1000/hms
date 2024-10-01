<template>
  <v-tabs right>
    <v-tab>Management Expense </v-tab>
    <v-tab>Non Management Expense</v-tab>
    <v-tab-item>
      <Expense :is_admin_expense="1" />
    </v-tab-item>
    <v-tab-item>
      <Expense :is_admin_expense="0" />
    </v-tab-item>
  </v-tabs>
</template>
