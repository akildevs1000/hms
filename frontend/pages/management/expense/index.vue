<template>
  <div v-if="can('management_expenses_access') && can('management_expenses_view')">
    <Expense :is_management="1" />
  </div>
  <NoAccess v-else />
</template>
<script>
import Expense from '../../../components/expense/Expense.vue';
export default {
  components: {
    Expense,
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
  },

};
</script>
