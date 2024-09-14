<template>
  <thead style="background-color: #f2f2f2; width: 100%">
    <tr style="background-color: #f2f2f2; width: 100%">
      <td v-for="(col,index) in cols" :key="index"
        style="border-top: 1px solid #bdbdbd; border-bottom: 1px solid #bdbdbd"
        class="text-center py-2"
      >
        <small>{{col}}</small>
      </td>
    </tr>
  </thead>
</template>
<script>
export default {
  props: ["cols"],
}
</script>
