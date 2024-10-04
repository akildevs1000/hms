<template>
  <div :style="`max-height: ${height || 150}px; overflow: auto`" class="px-1">
    <table cellspacing="0" style="width: 100%">
      <thead>
        <tr>
          <td
            v-for="(col, index) in headers"
            :key="index"
            class="primary--text py-1 px-2 border-top border-bottom"
            :class="`text-${col.align}`"
          >
            {{ col.text }}
          </td>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="index">
          <td
            v-for="(header, tdIndex) in headers"
            :key="tdIndex"
            :class="`text-${header.align}`"
            class="py-1 px-2 border-bottom text-color"
            :width="header.width"
          >
            <slot :name="header.value" :item="item">{{
              item[header.value]
            }}</slot>
          </td>
        </tr>
        <slot name="row"></slot>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: ["items", "headers","height"],
};
</script>
