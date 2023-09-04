<template>
    <div>
        <date-picker v-model="time3" range></date-picker>
    </div>
</template>
<script>
// import DateRangePicker2 from '../components/DateRangePicker2.vue';
import Expense from '../components/expense/Expense.vue';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
export default {
    components: {
        Expense,

        DatePicker
    },
    data() {
        return {

            time3: null,
        };
    },

};
</script>
  