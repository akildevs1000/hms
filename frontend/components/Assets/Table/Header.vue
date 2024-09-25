<template>
  <thead>
    <tr>
      <td
        v-for="(col, index) in cols"
        :key="index"
        :class="`text-${col.align}`"
      >
        <AssetsTextLabel color="primary" :label="col.text" />
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
  methods: {},
};
</script>

<style scoped>
td {
  border-top: 1px solid #bdbdbd;
  border-bottom: 1px solid #bdbdbd;
}
</style>
