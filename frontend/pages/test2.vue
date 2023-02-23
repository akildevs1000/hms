<template>
  <div>
    <ChildComponent @my-sent="child"></ChildComponent>
    <p>Data from child: {{ dataFromChild }}</p>
  </div>
</template>

<script>

import ChildComponent from '../components/ChildComponent'
export default {
  components: { ChildComponent },

  data() {
    return {
      dataFromChild: ''
    }
  },
  methods: {
    child(data) {
      this.dataFromChild = data
    }
  }
}
</script>
