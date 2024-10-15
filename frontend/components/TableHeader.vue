<template>
  <thead>
    <tr>
      <td
        v-for="(col, index) in cols"
        :key="index"
        class="primary--text"
        :class="isRightAligned(col) ? 'text-right' : 'text-center'"
      >
        {{ col }}
      </td>
    </tr>
  </thead>
</template>

<script>
export default {
  props: {
    cols: {
      type: Array,
      required: true,
    },
  },
  methods: {
    isRightAligned(col) {
      const rightAlignedColumns = [
        "Tax",
        "Sub Total",
        "Total",
        "Debit",
        "Credit",
        "Balance",
      ];
      return rightAlignedColumns.includes(col);
    },
  },
};
</script>

<style scoped>
td {
  border-top: 1px solid #bdbdbd;
  border-bottom: 1px solid #bdbdbd;
}
</style>
