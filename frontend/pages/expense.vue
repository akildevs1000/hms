<template>
  <div v-if="can(`expense_access`)">
    <v-tabs right>
      <v-tab v-if="can(`management_expense_access`)">Management Expense </v-tab>
      <v-tab>Non Management Expense</v-tab>
      <v-tab>Vendor</v-tab>
      <v-tab>Category</v-tab>

      <v-tab-item>
        <Expense :is_admin_expense="1" />
      </v-tab-item>
      <v-tab-item>
        <Expense :is_admin_expense="0" />
      </v-tab-item>
      <v-tab-item><Vendor /></v-tab-item>
      <v-tab-item><VendorCategory /></v-tab-item>

    </v-tabs>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
  },
};
</script>
