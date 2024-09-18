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
          :class="`${col !== 'Total' ? `text-center` : `text-left pl-3`}`"
        >
          <small>{{ col.text }}</small>
        </td>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in items" :key="index">
        <td
          v-for="(td, tdIndex) in headers"
          :key="tdIndex"
          class="text-center py-2"
          style="border-bottom: 1px solid #e0e0e0"
        >
          <small>{{ item[td.value] }}</small>
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
