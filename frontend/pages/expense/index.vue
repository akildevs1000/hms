<template>
  <div>
    <v-tabs right>
      <v-tab v-if="can(`expense_access`)">Expense</v-tab>
      <v-tab v-if="can(`vendors_access`)">Vendor</v-tab>
      <v-tab v-if="can(`expense_category_access`)">Category</v-tab>
      <v-tab-item v-if="can(`expense_access`)">
        <Expense />
      </v-tab-item>
      <v-tab-item v-if="can(`vendors_access`)"><Vendor /></v-tab-item>
      <v-tab-item v-if="can(`expense_category_access`)"
        ><VendorCategory
      /></v-tab-item>
    </v-tabs>
  </div>
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
