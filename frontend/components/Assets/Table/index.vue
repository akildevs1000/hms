<template>
  <table cellspacing="0" style="width: 100%">
    <thead>
      <tr>
        <td
          v-for="(col, index) in headers"
          :key="index"
          style="
            border-top: 1px solid #bdbdbd;
            border-bottom: 1px solid #bdbdbd;
          "
          class="primary--text py-2"
          :class="`text-${col.align}`"
        >
          <small>{{ col.text }}</small>
        </td>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in items" :key="index">
        <td
          v-for="(col, tdIndex) in headers"
          :key="tdIndex"
          :class="`text-${col.align}`"
          class="py-2"
          style="border-bottom: 1px solid #e0e0e0; color: #8a8a8a"
        >
          <small>{{ item[col.value] }}</small>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: ["items", "headers"],
};
</script>
