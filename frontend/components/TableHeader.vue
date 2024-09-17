<template>
  <thead>
    <tr>
      <td
        v-for="(col, index) in cols"
        :key="index"
        style="border-top: 1px solid #bdbdbd; border-bottom: 1px solid #bdbdbd"
        class="primary--text py-2 "
        :class="`${col !== 'Total' ? `text-center` : `text-left pl-3`}`"

      >
        <small>{{ col }}</small>
      </td>
    </tr>
  </thead>
</template>
<script>
export default {
  props: ["cols"],
};
</script>
