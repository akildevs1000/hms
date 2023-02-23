<template>
  <button @click="sendDataToParent">Send Data to Parent</button>
</template>

<script>
export default {
  name: 'ChildComponent',
  methods: {
    sendDataToParent() {
      this.$emit('my-sent', 'Hello from child component')
    }
  }
}
</script>
