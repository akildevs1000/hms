<template>
  <v-tabs right>
    <v-tab>Management Expense </v-tab>
    <v-tab>Non Management Expense</v-tab>
    <v-tab-item>
      <ExpenseManagement />
    </v-tab-item>
    <v-tab-item>
      <ExpenseNonManagement />
    </v-tab-item>
  </v-tabs>
</template>
