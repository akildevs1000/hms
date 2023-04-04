<template>
  <div>
    <Expense :is_management="1" />
  </div>
</template>
<script>
import Expense from '../../../components/expense/Expense.vue';
export default {
  components: {
    Expense,
  },

};
</script>
