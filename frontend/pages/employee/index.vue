<template>
  <v-tabs right>
    <v-tab>Employee</v-tab>
    <v-tab>House Keeping</v-tab>
    <v-tab>Maintenance</v-tab>
    <v-tab-item><User user_type="employee" /></v-tab-item>
    <v-tab-item><User user_type="house_keeping" /></v-tab-item>
    <v-tab-item><User user_type="maintenance" /></v-tab-item>
  </v-tabs>
</template>
