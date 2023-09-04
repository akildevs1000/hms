<template>
  <div>

    <Expense :is_management="0" />
  </div>
</template>
<script>
import Expense from '../components/expense/Expense.vue';
export default {
  components: {
    Expense,


  },
  data() {
    return {


    };
  },

};
</script>
