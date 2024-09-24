<template>
  <v-tabs right>
    <v-tab>Online</v-tab>
    <v-tab>Corporate</v-tab>
    <v-tab>Travel Agent</v-tab>
    <v-tab>Customer</v-tab>
    <v-tab-item>
      <Source type="online" />
    </v-tab-item>
    <v-tab-item>
      <Source type="corporate" />
    </v-tab-item>
    <v-tab-item>
      <Source type="agent" />
    </v-tab-item>
    <v-tab-item>
      <Customer />
    </v-tab-item>
  </v-tabs>
</template>
